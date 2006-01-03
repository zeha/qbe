/*
	$Id: QbeNP.c 71 2004-05-03 06:52:31Z ch $
	(C) Copyright 2004 Christian Hofstaedtler
*/


#define UNICODE
#define _UNICODE

#include <windows.h>
#include <lm.h>
#include <winhttp.h>
#include "../QbeGina/qbeldap.h"
#include "npapi.h"
#include "npaux.h"

#define HAVE_WRITELOGFILE
void WriteLogFile(LPTSTR String);

#include "../QbeGina/qbeldap.c"

/// The authentication type is only used here in a Unicode context
#define MSV1_0_AUTH_TYPE    L"MSV1_0:Interactive"

BOOL
WINAPI
DllMain(
       HANDLE hInst,
       DWORD dwReason,
       LPVOID lpReserved
       )
{
   if (dwReason == DLL_PROCESS_ATTACH)
   {
      DisableThreadLibraryCalls(hInst);
   }

   return TRUE;
}

BOOL qbe_sam_issasuser(LPWSTR szUsername);

/****************************************************************************
   FUNCTION: NPGetCaps

   PURPOSE:  This entry point is called to query the provider.  The parameter
             is an index representing the query.  For a credential manager
             only the following index values apply:

             WNNC_SPEC_VERSION - What version of the provider specification
                was used in developing this provider?  The return value is
                the version number.

             WNNC_DRIVER_VERSION - The version of the provider.

             WNNC_START - Will the provider start? When? The return values
                are:

                    - 0 : Only return this if the provider will *not* start.
                    - Estimated Start time in milliseconds : This is how
                        long the provider is expected to take to start.
                    - 0xFFFFFFFF : Time to start is unknown.
                    - 1 : Provider is already started.

             A return value of 0 in other cases indicates that the query
             is not supported.

*******************************************************************************/
DWORD
WINAPI
NPGetCaps(
         DWORD nIndex
         )
{
   DWORD dwRes;

   switch (nIndex)
   {

   case WNNC_NET_TYPE:
      dwRes = WNNC_CRED_MANAGER; // credential manager
      break;

   case WNNC_SPEC_VERSION:
      dwRes = WNNC_SPEC_VERSION51;  // We are using version 5.1 of the spec.
      break;

   case WNNC_DRIVER_VERSION:
      dwRes = 1;  // This driver is version 1.
      break;

   case WNNC_START:
      dwRes = 1;  // We are already "started"
      break;

   default:
      dwRes = 0;  // We don't support anything else
      break;
   }

   return dwRes;

}

BOOL getLocalMachineName(LPWSTR szWorkstationName)
{
	NET_API_STATUS netstatus;
	LPWKSTA_INFO_100 lpWkstaInfo;

	netstatus = NetApiBufferAllocate(sizeof(LPWKSTA_INFO_100), (LPVOID *) &lpWkstaInfo);
	if (netstatus == NERR_Success)
	{
		NetApiBufferFree(&lpWkstaInfo);
	}
	
	//
	// Get local machine name.
	//
	netstatus = NetWkstaGetInfo(NULL, 100, (LPBYTE *) &lpWkstaInfo);
	if (netstatus != NERR_Success)
	{
		while (netstatus == NERR_WkstaNotStarted)
		{
			Sleep(1000);
			// try again
			netstatus = NetWkstaGetInfo(NULL, 100, (LPBYTE *) &lpWkstaInfo);
			if (netstatus == NERR_Success)
				break;
			if (netstatus != NERR_WkstaNotStarted)
				return FALSE;
		}
	}            
	
	wcscpy(szWorkstationName,lpWkstaInfo->wki100_computername);

	NetApiBufferFree((LPVOID) lpWkstaInfo);
	
	return TRUE;
}

BOOL initPolicy(struct QbeSAS_HostPolicy* pPolicy)
{
	LPWSTR szLocalMachineName = (LPWSTR)malloc(256);

	if (!getLocalMachineName(szLocalMachineName))
	{
		return FALSE;
	}

	qbe_ldap_getpolicy(szLocalMachineName,pPolicy);

	return TRUE;
}	

///
///   FUNCTION: NPLogonNotify
///
///   PURPOSE:  This entry point is called when a user logs on.  If the user
///             authentication fails here, the user will still be logged on
///             to the local machine.
DWORD WINAPI NPLogonNotify (
              PLUID               lpLogonId,
              LPCWSTR             lpAuthentInfoType,
              LPVOID              lpAuthentInfo,
              LPCWSTR             lpPreviousAuthentInfoType,
              LPVOID              lpPreviousAuthentInfo,
              LPWSTR              lpStationName,
              LPVOID              StationHandle,
              LPWSTR              *lpLogonScript
              )
{
	struct QbeSAS_HostPolicy qHostPolicy;
	PMSV1_0_INTERACTIVE_LOGON pAuthInfo;

	// If the primary authenticator is not MSV1_0, return success.
	// Why? Because this is the only auth info structure that we
	// understand and we don't want to interact with other types.
	if ( lstrcmpiW (MSV1_0_AUTH_TYPE, lpAuthentInfoType) )
	{
		SetLastError(NO_ERROR);
		return NO_ERROR;
	}

	if ( lstrcmpiW (QBENP_INTERACTIVESTATION, lpStationName) )
	{
		SetLastError(NO_ERROR);
		return NO_ERROR;
	}
	
	// Do something with the authentication information
	pAuthInfo = (PMSV1_0_INTERACTIVE_LOGON) lpAuthentInfo;

	// Check if this user was created by QbeGina
	if (qbe_sam_issasuser(pAuthInfo->UserName.Buffer))
	{ // Yes
	if (initPolicy(&qHostPolicy))
	{
		// Haben wir ein LoginScript in der Hostpolicy?
		if (qHostPolicy.LoginScript != NULL)
		{
			// The Caller MUST free this memory
			*lpLogonScript = LocalAlloc(LPTR,1024);

			wsprintf(*lpLogonScript,TEXT("%s %s %s"),qHostPolicy.LoginScript,pAuthInfo->UserName.Buffer,pAuthInfo->Password.Buffer);
			WriteLogFile(*lpLogonScript);
		} else {
			// Nein.
			WriteLogFile(TEXT("No login script specified."));
		}
		WriteLogFile(TEXT("\r\n"));

		// map the network drive specified in policy
		{
			LPWSTR szUseName = (LPWSTR)malloc(512);
			LPWSTR szUnc = (LPWSTR)malloc(512);
			NETRESOURCE netRes;
			DWORD res;
			
			if ( (qHostPolicy.HomeDrive != NULL) && (qHostPolicy.HomeDriveDir != NULL) )
			{
				swprintf(szUseName,TEXT("%s:"),qHostPolicy.HomeDrive);
				if (NetUseDel(NULL, szUseName, USE_LOTS_OF_FORCE) != NERR_Success)
					WriteLogFile(TEXT("*** NetUseDel failed.\r\n"));

				if (szUseName == NULL)
					MessageBox(NULL,TEXT("Error allocating szUseName!"),TEXT("Error"),0);
				if (szUnc == NULL)
					MessageBox(NULL,TEXT("Error allocating szUnc!"),TEXT("Error"),0);
					
				netRes.dwType = RESOURCETYPE_DISK;
				netRes.lpProvider = NULL;
				netRes.lpLocalName = szUseName;
				swprintf(szUnc,qHostPolicy.HomeDriveDir,pAuthInfo->UserName.Buffer);
				netRes.lpRemoteName = szUnc;
			
				res = WNetAddConnection2(&netRes,pAuthInfo->UserName.Buffer,pAuthInfo->Password.Buffer,FALSE);
			       	if (res != NO_ERROR)
				{
					WriteLogFile(TEXT("*** WNetAddConnection2 failed.\r\n"));
					WriteLogFile(TEXT("*** Error code: "));
					_ltow(res,szUnc,10);
					WriteLogFile(szUnc);
					WriteLogFile(TEXT("\r\n"));
				}
			}

			free(szUseName);
			free(szUnc);
		}
	} else {
		WriteLogFile(TEXT("Cant init policy object\r\n"));
		MessageBox(NULL,TEXT("The machine policy could not be found in LDAP."),TEXT("Qbe SAS Client"),MB_OK|MB_ICONERROR);
	}
	} else {
		WriteLogFile(TEXT("User is not a Qbe SAS User\r\n"));
	}
	
	SetLastError(NO_ERROR);
	return NO_ERROR;
}

BOOL qbe_sam_issasuser(LPWSTR szUsername)
{
	LPLOCALGROUP_USERS_INFO_0 pBuf = NULL;
	DWORD dwPrefMaxLen = MAX_PREFERRED_LENGTH;
	DWORD dwEntriesRead = 0;
	DWORD dwTotalEntries = 0;
	NET_API_STATUS nStatus;
	BOOL bReturn = FALSE;

	//
	// Call the NetUserGetLocalGroups function 
	//  specifying information level 0.
	//
	//  The LG_INCLUDE_INDIRECT flag specifies that the 
	//   function should also return the names of the local 
	//   groups in which the user is indirectly a member.
	//
	nStatus = NetUserGetLocalGroups(NULL,
                                   szUsername,
                                   0,
                                   0,
                                   (LPBYTE *) &pBuf,
                                   dwPrefMaxLen,
                                   &dwEntriesRead,
                                   &dwTotalEntries);
   //
   // If the call succeeds,
   //
   if (nStatus == NERR_Success)
   {
      LPLOCALGROUP_USERS_INFO_0 pTmpBuf;
      DWORD i;

      if ((pTmpBuf = pBuf) != NULL)
      {
         //
         // Loop through the entries and 
         //  print the names of the local groups 
         //  to which the user belongs. 
         //
         for (i = 0; i < dwEntriesRead; i++)
         {
            if (pTmpBuf == NULL)
               break;

            //wprintf(L"\t-- %s\n", pTmpBuf->lgrui0_name);
		if (wcscmp(TEXT("Qbe SAS Users"), pTmpBuf->lgrui0_name) == 0)
		{
			bReturn = TRUE;
			break;
		}

            pTmpBuf++;
         }
      }
   }
   //
   // Free the allocated memory.
   //
   if (pBuf != NULL)
      NetApiBufferFree(pBuf);

   return bReturn;
}


/****************************************************************************
   FUNCTION: NPPasswordChangeNotify

   PURPOSE:  This function is used to notify a credential manager provider
             of a password change (or, more accurately, an authentication
             information change) for an account.

	     SAS Info:
	     Hier könnte man die Passwortänderung abfangen und an den Server
	     übertragen. Wichtig dabei ist jedoch eine entsprechende
	     Verschlüsselung und Authentifizierung des alten Passworts.

*******************************************************************************/
DWORD
WINAPI
NPPasswordChangeNotify (
                       LPCWSTR             lpAuthentInfoType,
                       LPVOID              lpAuthentInfo,
                       LPCWSTR             lpPreviousAuthentInfoType,
                       LPVOID              lpPreviousAuthentInfo,
                       LPWSTR              lpStationName,
                       LPVOID              StationHandle,
                       DWORD               dwChangeInfo
                       )
{
	SetLastError(NO_ERROR);
	return NO_ERROR;
}

/// Debug stuff
void WriteLogFile(
            LPTSTR String
            )
{
#ifdef SASDEBUG
   HANDLE hFile;
   DWORD dwBytesWritten;

   hFile = CreateFile(
                     TEXT("c:\\np.log"),
                     GENERIC_WRITE,
                     0,
                     NULL,
                     OPEN_ALWAYS,
                     FILE_FLAG_SEQUENTIAL_SCAN,
                     NULL
                     );

   if (hFile == INVALID_HANDLE_VALUE) return;

   //
   // Seek to the end of the file
   //
   SetFilePointer(hFile, 0, NULL, FILE_END);

   WriteFile(
            hFile,
            String,
            lstrlen(String)*sizeof(TCHAR),
            &dwBytesWritten,
            NULL
            );

   CloseHandle(hFile);
#endif
   return;
}


