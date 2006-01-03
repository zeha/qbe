/*
 * $Id: QbeGina.c,v 1.5 2004/03/02 14:49:34 ch Exp $
 * (C) Copyright 2002-2003 Christian Hofstaedtler
 * (C) Copyright 1996-2000 Microsoft Corporation.  All rights reserved.
 *
 * Hauptmodul des GINA-Authentikationsmoduls. Wird anstelle der MSGINA.DLL geladen,
 * und reicht alle Systemaufrufe an diese weiter. 
 * Bei einem erfolgreichem Systemlogin wird der Benutzername und das Passwort in einen
 * Shared-Memory Bereich kopiert und freigegeben.
 * 
 */

#if _WIN32_WINNT < 0x0500
#undef _WIN32_WINNT
#define _WIN32_WINNT 0x0500
#endif

#define UNICODE
#define _UNICODE

#undef VC_EXTRALEAN

#include <windows.h>
#include <winwlx.h>
#include <lm.h>
#include <tchar.h>
#include <stdio.h>
#include <direct.h>
#include <winnetwk.h>

#include <Winsvc.h>
#include <Mmsystem.h>
#include <process.h>

#include "resource.h"
#include "QbeGina.h"

#include "qbeldap.h"
#include "qbereg.h"
#include "qbesvc.h"


/// Nichts ist passiert.
#define QBEGINA_LOGINACTION_NONE	0
/// Der Benutzer wird angemeldet.
#define QBEGINA_LOGINACTION_LOGON	1
/// System wird heruntergefahren.
#define QBEGINA_LOGINACTION_SHUTDOWN	2
/// WinLogon Event
#define QBEGINA_LOGINACTION_WINLOGON	3


/// Name der Original MSGINA.DLL
/// Da dieser Name fix codiert ist, kann die SASGina nicht mit anderen Gina-Modulen verwendet werden.
#define REALGINA_PATH      TEXT("MSGINA.DLL")

/// Versionsnummer des System-APIs dass von unserer SASGina unterstützt wird. 
#define GINASTUB_VERSION   (WLX_VERSION_1_3) 

/* Globable Variablen für WinLogon */
static PVOID g_pWinlogon = NULL;
static DWORD g_dwVersion = WLX_VERSION_1_3;
static HANDLE g_hWlx = NULL;
static HINSTANCE g_hDll = NULL;
static HINSTANCE g_hSASGinaDll = NULL;

/* nur fuer uns */
/// Sand-Uhr Mauszeiger Handle
static HCURSOR hWaitCursor = NULL;
/// Qbe Logo Bild Handle
static HBITMAP hLogoBitmap = NULL;
/// Drücken Sie Ctrl-Alt-Del-... Bild Handle
static HBITMAP hCtrlAltDelBitmap = NULL;
/// Unsere HostPolicy
static struct QbeSAS_HostPolicy *g_HostPolicy = NULL;

/// Pointer zu den Funktionen der echten MSGina.DLL, werden zur Laufzeit importiert.
static PFWLXNEGOTIATE            pfWlxNegotiate;
static PFWLXINITIALIZE           pfWlxInitialize;
static PFWLXDISPLAYSASNOTICE     pfWlxDisplaySASNotice;
static PFWLXLOGGEDOUTSAS         pfWlxLoggedOutSAS;
static PFWLXACTIVATEUSERSHELL    pfWlxActivateUserShell;
static PFWLXLOGGEDONSAS          pfWlxLoggedOnSAS;
static PFWLXDISPLAYLOCKEDNOTICE  pfWlxDisplayLockedNotice;
static PFWLXWKSTALOCKEDSAS       pfWlxWkstaLockedSAS;
static PFWLXISLOCKOK             pfWlxIsLockOk;
static PFWLXISLOGOFFOK           pfWlxIsLogoffOk;
static PFWLXLOGOFF               pfWlxLogoff;
static PFWLXSHUTDOWN             pfWlxShutdown;
// New for version 1.1
static PFWLXSTARTAPPLICATION     pfWlxStartApplication  = NULL;
static PFWLXSCREENSAVERNOTIFY    pfWlxScreenSaverNotify = NULL;
// New for version 1.2 - No new GINA interface was added, except a new function in the dispatch table.
// New for version 1.3
static PFWLXNETWORKPROVIDERLOAD  pfWlxNetworkProviderLoad  = NULL;
static PFWLXDISPLAYSTATUSMESSAGE pfWlxDisplayStatusMessage = NULL;
static PFWLXGETSTATUSMESSAGE     pfWlxGetStatusMessage     = NULL;
static PFWLXREMOVESTATUSMESSAGE  pfWlxRemoveStatusMessage  = NULL;

//static PFWLXGETCONSOLESWITCHCREDENTIALS pfWlxGetConsoleSwitchCredentials = NULL;

typedef DWORD (STDAPICALLTYPE*PFNSHELLSHUTDOWNDIALOG)(HWND hwndParent, LPCTSTR szUsername, DWORD dwExcludeItems);
static PFNSHELLSHUTDOWNDIALOG    pfnShellShutdownDialog = NULL;

BOOL initPolicy();

/// Schreibt (falls mit SASDEBUG) aktiviert den "String" nach c:\\gina.txt
void WriteLogFile(LPTSTR String)
{
// Ohne SASDEBUG ist das eine NOP.
#ifdef SASDEBUG
   HANDLE hFile;
   DWORD dwBytesWritten;

   hFile = CreateFile(
                     TEXT("c:\\gina.txt"),
                     GENERIC_WRITE,
                     0,
                     NULL,
                     OPEN_ALWAYS,
                     FILE_FLAG_SEQUENTIAL_SCAN,
                     NULL
                     );

   if (hFile == INVALID_HANDLE_VALUE) return;

   // Seek to the end of the file
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

/// Funktion um den QbeSvc zu starten.
DWORD StartRPCService() 
{ 
	DWORD dwStatus;

	SC_HANDLE schSCManager;
	SC_HANDLE schService;
	
	schSCManager = OpenSCManager(
		NULL,
		NULL,
		SC_MANAGER_ALL_ACCESS);

	WriteLogFile(TEXT("starting qbeservice\r\n"));

	schService = OpenService( 
		schSCManager,          // SCM database 
        TEXT("qbesvc"),              // service name
        SERVICE_ALL_ACCESS); 
 
	if (schService == NULL) 
	{ 
		dwStatus = FALSE;
		goto closeservice;
	}
 
	// Konfiguration vom QbeSvc abaendern, so dass dieser
	// immer automatisch gestartet wird
	ChangeServiceConfig (
			schService,
			SERVICE_NO_CHANGE,
			SERVICE_AUTO_START,
			SERVICE_NO_CHANGE,
			NULL,
			NULL,
			NULL,
			NULL,
			NULL,
			NULL,
			NULL);

	// QbeSvc starten
	dwStatus = StartService(
            schService,  // handle to service 
            0,           // number of arguments 
            NULL);      // no arguments 

	if (!dwStatus)
	{	// Fehler!?
		dwStatus = GetLastError();
		if (dwStatus == ERROR_SERVICE_ALREADY_RUNNING)
		{	// QbeSvc laeuft bereits, das ist fuer uns kein Fehler
			dwStatus = TRUE;
		} else {
			dwStatus = FALSE;
		}

	} else {
		dwStatus = TRUE;
		SleepEx(500,TRUE);
	}

closeservice:
	CloseServiceHandle(schService); 
	CloseServiceHandle(schSCManager);
	return dwStatus;
}

/// Einsprungspunkt der QbeGina.DLL
BOOL WINAPI DllMain(HINSTANCE hInstance,DWORD dwReason,LPVOID lpReserved)
{
	switch(dwReason)
	{
	case DLL_PROCESS_ATTACH:
		// hInstance speichern
		g_hSASGinaDll = hInstance;
		// Bitmaps laden
		hLogoBitmap = LoadBitmap(g_hSASGinaDll,MAKEINTRESOURCE(IDB_SYSLOGIN));
		hCtrlAltDelBitmap = LoadBitmap(g_hSASGinaDll,MAKEINTRESOURCE(IDB_CTRLALTDEL));
		// Fall through and return true
	case DLL_PROCESS_DETACH:
	default:
		return(TRUE);
	}
}

/// Hook into the real MSGINA.
BOOL MyInitialize (HINSTANCE hDll, DWORD dwWlxVersion)
{
	g_hDll = hDll;
   //
   // Get pointers to all of the WLX functions in the real MSGINA.
   //
   pfWlxInitialize = 
      (PFWLXINITIALIZE) GetProcAddress(hDll, "WlxInitialize");
   if (!pfWlxInitialize) 
   {
      return FALSE;
   }

   pfWlxDisplaySASNotice =
      (PFWLXDISPLAYSASNOTICE) GetProcAddress(hDll, "WlxDisplaySASNotice");
   if (!pfWlxDisplaySASNotice) 
   {
      return FALSE;
   }

   pfWlxLoggedOutSAS = 
      (PFWLXLOGGEDOUTSAS) GetProcAddress(hDll, "WlxLoggedOutSAS");
   if (!pfWlxLoggedOutSAS) 
   {
      return FALSE;
   }

   pfWlxActivateUserShell =
      (PFWLXACTIVATEUSERSHELL) GetProcAddress(hDll, "WlxActivateUserShell");
   if (!pfWlxActivateUserShell) 
   {
      return FALSE;
   }

   pfWlxLoggedOnSAS =
      (PFWLXLOGGEDONSAS) GetProcAddress(hDll, "WlxLoggedOnSAS");
   if (!pfWlxLoggedOnSAS) 
   {
      return FALSE;
   }

   pfWlxDisplayLockedNotice =
      (PFWLXDISPLAYLOCKEDNOTICE) GetProcAddress(hDll, "WlxDisplayLockedNotice");
   if (!pfWlxDisplayLockedNotice) 
   {
      return FALSE;
   }

   pfWlxIsLockOk = 
      (PFWLXISLOCKOK) GetProcAddress(hDll, "WlxIsLockOk");
   if (!pfWlxIsLockOk) 
   {
      return FALSE;
   }

   pfWlxWkstaLockedSAS =
       (PFWLXWKSTALOCKEDSAS) GetProcAddress(hDll, "WlxWkstaLockedSAS");
   if (!pfWlxWkstaLockedSAS) 
   {
      return FALSE;
   }

   pfWlxIsLogoffOk = 
      (PFWLXISLOGOFFOK) GetProcAddress(hDll, "WlxIsLogoffOk");
   if (!pfWlxIsLogoffOk) 
   {
      return FALSE;
   }

   pfWlxLogoff = 
      (PFWLXLOGOFF) GetProcAddress(hDll, "WlxLogoff");
   if (!pfWlxLogoff) 
   {
      return FALSE;
   }

   pfWlxShutdown = 
      (PFWLXSHUTDOWN) GetProcAddress(hDll, "WlxShutdown");
   if (!pfWlxShutdown) 
   {
      return FALSE;
   }

   //	PFNSHELLSHUTDOWNDIALOG pfnShellShutdownDialog = (PFNSHELLSHUTDOWNDIALOG)GetProcAddress(hGina, "ShellShutdownDialog");
   pfnShellShutdownDialog =
      (PFNSHELLSHUTDOWNDIALOG) GetProcAddress(hDll, "ShellShutdownDialog");
   if (!pfnShellShutdownDialog)
   {
      return FALSE;
   }

   //
   // Load functions for version 1.1 as necessary.
   //
   if (dwWlxVersion > WLX_VERSION_1_0)
   {
      pfWlxStartApplication = 
         (PFWLXSTARTAPPLICATION) GetProcAddress(hDll, "WlxStartApplication");
      if (!pfWlxStartApplication)
      {
         return FALSE;
      }

      pfWlxScreenSaverNotify = 
         (PFWLXSCREENSAVERNOTIFY) GetProcAddress(hDll, "WlxScreenSaverNotify");
      if (!pfWlxScreenSaverNotify)
      {
         return FALSE;
      }
   }

   //
   // Load functions for version 1.3 as necessary.
   //
   if (dwWlxVersion > WLX_VERSION_1_2)
   {
      pfWlxNetworkProviderLoad = 
         (PFWLXNETWORKPROVIDERLOAD) 
            GetProcAddress(hDll, "WlxNetworkProviderLoad");
      if (!pfWlxNetworkProviderLoad)
      {
         return FALSE;
      }

      pfWlxDisplayStatusMessage =
         (PFWLXDISPLAYSTATUSMESSAGE) 
            GetProcAddress(hDll, "WlxDisplayStatusMessage");
      if (!pfWlxDisplayStatusMessage)
      {
         return FALSE;
      }

      pfWlxGetStatusMessage =
         (PFWLXGETSTATUSMESSAGE)
            GetProcAddress(hDll, "WlxGetStatusMessage");
      if (!pfWlxGetStatusMessage)
      {
         return FALSE;
      }

      pfWlxRemoveStatusMessage =
         (PFWLXREMOVESTATUSMESSAGE)
            GetProcAddress(hDll, "WlxRemoveStatusMessage");
      if (!pfWlxRemoveStatusMessage)
      {
         return FALSE;
      }
   }
   
   // Mauszeiger laden
   hWaitCursor = LoadCursor(NULL, IDC_WAIT);

   // QbeSvc starten
   StartRPCService();
   
   // OK
   return TRUE;
}


BOOL 
WINAPI 
WlxNegotiate (DWORD   dwWinlogonVersion,
              DWORD * pdwDllVersion)
{
   HINSTANCE hDll;
   DWORD dwWlxVersion = GINASTUB_VERSION;

   // MSGINA.DLL Laden
   if (!(hDll = LoadLibrary(REALGINA_PATH))) 
   {
      return FALSE;
   }

   // WlxNegotiate importieren
   pfWlxNegotiate = (PFWLXNEGOTIATE) GetProcAddress(hDll, "WlxNegotiate");
   if (!pfWlxNegotiate) 
   {
      return FALSE;
   }
 
   /* Check fuer alte System-API */
   if (dwWinlogonVersion < dwWlxVersion)
   {
      dwWlxVersion = dwWinlogonVersion;
   }

   /* MSGINA Negotiate aufrufen um die Version abzustimmen */
   if (!pfWlxNegotiate(dwWlxVersion, &dwWlxVersion))
   {
      return FALSE;
   }
   
   /* Alles weiter importieren */
   if (!MyInitialize(hDll, dwWlxVersion))
   {
      return FALSE;
   }

   /* System-API-Version an WinLogon zurueckliefern */
   *pdwDllVersion = g_dwVersion = dwWlxVersion;


   return TRUE;
}



BOOL
WINAPI
WlxInitialize (LPWSTR  lpWinsta,
               HANDLE  hWlx,
               PVOID   pvReserved,
               PVOID   pWinlogonFunctions,
               PVOID * pWlxContext)
{
   //
   // Save pointer to dispatch table.
   // 
   // Note that g_pWinlogon will need to be properly casted to the 
   // appropriate version when used to call function in the dispatch 
   // table.
   //
   // For example, assuming we are at WLX_VERSION_1_3, we would call
   // WlxSasNotify() as follows:
   //
   // ((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxSasNotify(hWlx, MY_SAS);
   //
   g_pWinlogon = pWinlogonFunctions;
   g_hWlx = hWlx;

   return pfWlxInitialize(lpWinsta,
                          hWlx,
                          pvReserved,
                          pWinlogonFunctions,
                          pWlxContext);
}

/// GUI Handlerfunktion fuer die CTRL-ALT-DEL Dialogbox
BOOL 
CALLBACK 
DialogProc_SASNotice (HWND   hwndDlg,  // handle to dialog box
                          UINT   uMsg,     // message  
                          WPARAM wParam,   // first message parameter
                          LPARAM lParam)   // second message parameter
{
	switch (uMsg)
	{
	case WM_INITDIALOG:
		{
			SetWindowText (hwndDlg, TEXT("Qbe Network Logon") );
			// fall through
		}
	case WM_PAINT:
		{
			HBRUSH hBrush;
			HDC hDC;
			RECT rect;

			GetClientRect( hwndDlg, &rect );
			rect.bottom = 110;
			
			hDC = GetDC(hwndDlg);
			hBrush = CreateSolidBrush(RGB(0x33,0x66,0x99));
			FillRect(hDC, &rect, hBrush);

			DeleteObject(hBrush);

			GetClientRect( hwndDlg, &rect );
			rect.top = 110;
			
			hDC = GetDC(hwndDlg);
			hBrush = CreateSolidBrush(RGB(0xFF,0xFF,0xFF));
			FillRect(hDC, &rect, hBrush);
			
			DeleteObject(hBrush);
			
			SendDlgItemMessage(hwndDlg, IDC_LOGO, STM_SETIMAGE, IMAGE_BITMAP, (LPARAM)hLogoBitmap);
			SendDlgItemMessage(hwndDlg, IDC_CTRLALTDEL, STM_SETIMAGE, IMAGE_BITMAP, (LPARAM)hCtrlAltDelBitmap);

		return FALSE;
		}
	default:
		return FALSE;
	}
	return FALSE;
}
		

VOID
WINAPI
WlxDisplaySASNotice (PVOID pWlxContext)
{
	initPolicy();
	switch (g_dwVersion)	// check winlogon version, 1.2 and smaller probably crash
	{
	case WLX_VERSION_1_3:
		{	
		((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxDialogBox( g_hWlx,
			g_hSASGinaDll,
			MAKEINTRESOURCEW(IDD_SAS_CTRLALTDEL),
			NULL,
			DialogProc_SASNotice);
		break;
		}
	case WLX_VERSION_1_4:
	default:
		{	
		((PWLX_DISPATCH_VERSION_1_4) g_pWinlogon)->WlxDialogBox( g_hWlx,
			g_hSASGinaDll,
			MAKEINTRESOURCEW(IDD_SAS_CTRLALTDEL),
			NULL,
			DialogProc_SASNotice);
		break;
		}
	}
}

/// Das Fenster in hwnd am Bildschirm zentrieren.
VOID CenterWindow(HWND hwnd)
{
  RECT    rect;
  LONG    dx, dy;
  LONG    dxParent, dyParent;
  LONG    Style;
  GetWindowRect(hwnd, &rect);
  dx = rect.right - rect.left;
  dy = rect.bottom - rect.top;
  Style = GetWindowLong(hwnd, GWL_STYLE);
  if ((Style & WS_CHILD) == 0) 
    {
      dxParent = GetSystemMetrics(SM_CXSCREEN);
      dyParent = GetSystemMetrics(SM_CYSCREEN);
    } 
  else 
    {
      HWND    hwndParent;
      RECT    rectParent;
      hwndParent = GetParent(hwnd);
      if (hwndParent == NULL)
	  hwndParent = GetDesktopWindow();
      GetWindowRect(hwndParent, &rectParent);
      dxParent = rectParent.right - rectParent.left;
      dyParent = rectParent.bottom - rectParent.top;
    }
  rect.left = (dxParent - dx) / 2;
  rect.top  = (dyParent - dy) / 3;
  SetWindowPos(hwnd, HWND_TOPMOST, rect.left, rect.top, 320, 152, SWP_NOCOPYBITS);	//SWP_NOSIZE
  SetForegroundWindow(hwnd);
}

/// Liefert den Computernamen zurück
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

/// Initialisiert das HostPolicy Objekt
BOOL initPolicy()
{
	LPWSTR szLocalMachineName = (LPWSTR)malloc(256);
	if (g_HostPolicy==NULL)
	{
		g_HostPolicy = (struct QbeSAS_HostPolicy*)malloc(sizeof(struct QbeSAS_HostPolicy));
	} else {
		return TRUE;
	}

	if (!getLocalMachineName(szLocalMachineName))
	{
		free(g_HostPolicy);
		g_HostPolicy = NULL;
		return FALSE;
	}

	qbe_ldap_getpolicy(szLocalMachineName,g_HostPolicy);

	return TRUE;
}	

/// GUI Handlerfunktion fuer die Anmeldedialogbox
BOOL CALLBACK MyLoginBoxProc (HWND   hwndDlg,  // handle to dialog box
                          UINT   uMsg,     // message  
                          WPARAM wParam,   // first message parameter
                          LPARAM lParam)   // second message parameter
{
	switch (uMsg)
	{
//	case WM_REPAINT:
	case WM_PAINT:
		{
			HBRUSH hBrush;
			HDC hDC;
			RECT rect;

			GetClientRect( hwndDlg, &rect );
			rect.bottom = 110;
			
			hDC = GetDC(hwndDlg);
			hBrush = CreateSolidBrush(RGB(0x33,0x66,0x99));
			FillRect(hDC, &rect, hBrush);

			DeleteObject(hBrush);

			SendDlgItemMessage(hwndDlg, IDC_LOGO, STM_SETIMAGE, IMAGE_BITMAP, (LPARAM)hLogoBitmap);

		return FALSE;
		}
	case WM_INITDIALOG:
		{
			// Hourglass...
			HCURSOR hPrevCursor = SetCursor(hWaitCursor);

			// Bitmap setzen
			SendDlgItemMessage(hwndDlg, IDC_LOGO, STM_SETIMAGE, IMAGE_BITMAP, (LPARAM)hLogoBitmap);
			
			// Dialog-Titel setzen
			SetWindowText (hwndDlg, TEXT("Qbe Network Logon") );

			if (hPrevCursor != NULL)
				SetCursor(hPrevCursor);
			
			// Fokus auf das Username-Feld setzen
			SetFocus(GetDlgItem(hwndDlg, IDC_WLXLOGGEDOUTSAS_USERNAME)); 
			return FALSE;
		}
	case WM_COMMAND:
		{
			switch (LOWORD(wParam))
			{
				case IDOK:	//ok
					if (HIWORD(wParam) == BN_CLICKED)
					{
						DWORD QbeLdapReturnCode = -1;
						HCURSOR hPrevCursor = SetCursor(hWaitCursor);
						LPWSTR wszTemp = (PWSTR)malloc(1024);

						// get user + pass
						GetDlgItemText(hwndDlg,IDC_WLXLOGGEDOUTSAS_USERNAME,wszTemp,1023);
						g_AccountInfo.pszUsername = wcsdup(wszTemp);

						GetDlgItemText(hwndDlg,IDC_WLXLOGGEDOUTSAS_PASSWORD,wszTemp,1023);
						g_AccountInfo.pszPassword = wcsdup(wszTemp);

						EnableWindow(GetDlgItem(hwndDlg,IDC_WLXLOGGEDOUTSAS_USERNAME),FALSE);
						EnableWindow(GetDlgItem(hwndDlg,IDC_WLXLOGGEDOUTSAS_PASSWORD),FALSE);

						free(wszTemp);

						// check what ldap says
						QbeLdapReturnCode = qbe_ldap_checkuser(g_AccountInfo.pszUsername,g_AccountInfo.pszPassword);
						if (QbeLdapReturnCode == 0)
						{
							// successful LDAP login
							SetCursor(hPrevCursor);
							g_AccountInfo.bServerAuth = TRUE;
							EndDialog(hwndDlg, QBEGINA_LOGINACTION_LOGON);
							return FALSE;
						} else {
							// bleh, cant logon to ldap
							if (QbeLdapReturnCode == -2)
							{
								// cant connect to server!? - let the user try a local login
								if (IDYES == ((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxMessageBox(g_hWlx,hwndDlg, TEXT("Connection to LDAP server lost. Do you want to try a local login?"), TEXT("Qbe SAS Client"), MB_YESNO|MB_ICONEXCLAMATION))
								{
									HANDLE hUser;
									if(LogonUser(g_AccountInfo.pszUsername,TEXT(""),g_AccountInfo.pszPassword,LOGON32_LOGON_INTERACTIVE,LOGON32_PROVIDER_DEFAULT, &hUser))
									{	// local login ok.
										CloseHandle(hUser);
										g_AccountInfo.bServerAuth = FALSE;
										SetCursor(hPrevCursor);
										EndDialog(hwndDlg, QBEGINA_LOGINACTION_LOGON);
										return FALSE;
									} else {
										// fail.
										((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxMessageBox(g_hWlx,hwndDlg, TEXT("Local logon failed for your user."), TEXT("Qbe SAS Client"), MB_OK|MB_ICONERROR);
									}
								}
							} else {
								((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxMessageBox(g_hWlx,hwndDlg, TEXT("Network login failed."), TEXT("Qbe SAS Client"), MB_OK|MB_ICONERROR);

							}
						}

						SetDlgItemText(hwndDlg, IDC_WLXLOGGEDOUTSAS_USERNAME,TEXT(""));
						SetDlgItemText(hwndDlg, IDC_WLXLOGGEDOUTSAS_PASSWORD,TEXT(""));
						SetFocus(GetDlgItem(hwndDlg, IDC_WLXLOGGEDOUTSAS_USERNAME));
						SetCursor(hPrevCursor);
						EndDialog(hwndDlg, QBEGINA_LOGINACTION_NONE);
						return FALSE;
					}
					break;
				case IDCANCEL: //cancel
					if (HIWORD(wParam) == BN_CLICKED)
					{
						SetDlgItemText(hwndDlg, IDC_WLXLOGGEDOUTSAS_USERNAME,TEXT(""));
						SetDlgItemText(hwndDlg, IDC_WLXLOGGEDOUTSAS_PASSWORD,TEXT(""));
						SetFocus(GetDlgItem(hwndDlg, IDC_WLXLOGGEDOUTSAS_USERNAME));
						EndDialog(hwndDlg, QBEGINA_LOGINACTION_NONE);		//LOGIN_CANCELED
						return FALSE;
					}
					break;
				case IDC_SHUTDOWN:
				{
					TCHAR szUsername[500];
					szUsername[0] = TEXT('\0');
					
					switch (pfnShellShutdownDialog(NULL, szUsername, 1))
					{
					case 2:
						EndDialog(hwndDlg, QBEGINA_LOGINACTION_SHUTDOWN);
						break;
					case 4:
						EndDialog(hwndDlg, WLX_SAS_ACTION_SHUTDOWN_REBOOT);
						break;
					case 16:
						EndDialog(hwndDlg, WLX_SAS_ACTION_SHUTDOWN_SLEEP);
						break;
					case 64:
						EndDialog(hwndDlg, WLX_SAS_ACTION_SHUTDOWN_HIBERNATE);
						break;
					case 0:
					case 1:
						default:
						break;
					}
								
					return FALSE;
				}
		default:
			return FALSE;
		}
		}
	default:
		return FALSE;
	}
	return FALSE;
}


int qbe_sam_createuser(LPWSTR szUsername, LPWSTR szPassword)
{
	// for the user object
	USER_INFO_2 ui;
	USER_INFO_1006 ui1006;
	USER_INFO_1053 ui1053;
	NET_API_STATUS nStatus;
	WCHAR szHomeDirectory[512];

	// user->admin group
	SID_IDENTIFIER_AUTHORITY sidNTAuthority = SECURITY_NT_AUTHORITY;
	PSID pSidAdmins = NULL;
	
	WCHAR szAdminGroupName[UNLEN+1];
	ULONG ulAdminGroupName = UNLEN+1;
	WCHAR szLocalDomainName[UNLEN+1];
	ULONG ulLocalDomainName = UNLEN+1;
	
	SID_NAME_USE sidNameUse;
	LOCALGROUP_MEMBERS_INFO_3 localgroupMemberInfo;
	
	int rc = 0;		// we pretend we are ok :>
	
	ui.usri2_name = szUsername;
	ui.usri2_password = szPassword;
	ui.usri2_home_dir = NULL; //szHomeDirectory;
	ui.usri2_comment = TEXT("Qbe SAS User");
	ui.usri2_priv = USER_PRIV_USER;
	ui.usri2_flags = UF_PASSWD_CANT_CHANGE|UF_DONT_EXPIRE_PASSWD|UF_NORMAL_ACCOUNT;
	ui.usri2_logon_server = TEXT("qbe-auth");

	ui.usri2_logon_hours = NULL;
	ui.usri2_country_code = 0;
	ui.usri2_code_page = 0;
	ui.usri2_acct_expires = TIMEQ_FOREVER;
	ui.usri2_max_storage = USER_MAXSTORAGE_UNLIMITED;
	ui.usri2_auth_flags = 0;
	ui.usri2_workstations = NULL;
	ui.usri2_full_name = NULL;
	ui.usri2_usr_comment = NULL;
	ui.usri2_parms = NULL;
	ui.usri2_script_path = NULL;
	
	nStatus = NetUserAdd( NULL, 1, (LPBYTE)&ui, NULL );
	if ( (nStatus == NERR_UserExists) && (wcscmp(szUsername,TEXT("Administrator")) != 0) )
	{
		// delete user (if not teh admin) and then recreate it
		NetUserDel(NULL, szUsername);
		nStatus = NetUserAdd(NULL, 1, (LPBYTE)&ui, NULL);
		if (nStatus == NERR_UserExists)
			rc = -2;	// cant create user
	}


	if (g_HostPolicy->HomeDriveDir != NULL)
	{
		swprintf(szHomeDirectory, g_HostPolicy->HomeDriveDir, szUsername);
	} else {
		// some defaults
		swprintf(szHomeDirectory, TEXT("\\\\qbe-auth\\%s"), szUsername);
	}
	ui1006.usri1006_home_dir = szHomeDirectory;
	NetUserSetInfo( NULL, szUsername, 1006, (LPBYTE)&ui1006, NULL);

	if (g_HostPolicy->HomeDrive != NULL)
	{
		ui1053.usri1053_home_dir_drive = g_HostPolicy->HomeDrive;
	} else {
		// some defaults
		ui1053.usri1053_home_dir_drive = TEXT("Q");
	}
	NetUserSetInfo( NULL, szUsername, 1053, (LPBYTE)&ui1053, NULL);

	if (g_HostPolicy->DynamicUserGroup != NULL)
	{
		// group mappings
		BOOL bGotGroupName = FALSE;
		localgroupMemberInfo.lgrmi3_domainandname = szUsername;
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN/Administrators")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_ADMINS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN\\Administrators")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_ADMINS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN/Power Users")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_POWER_USERS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN\\Power Users")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_POWER_USERS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN/Users")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_USERS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN\\Users")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_USERS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN/Guests")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_GUESTS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if ((!bGotGroupName) && (wcscmp(g_HostPolicy->DynamicUserGroup,TEXT("BUILTIN\\Guests")) == 0))
		{
			AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_GUESTS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
			LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);
			bGotGroupName = TRUE;
		}
		if (!bGotGroupName)
		{
			wcscpy(szAdminGroupName, g_HostPolicy->DynamicUserGroup);
		}

		nStatus = NetLocalGroupAddMembers(NULL, szAdminGroupName, 3, (LPBYTE)&localgroupMemberInfo, 1);
		if (nStatus != NERR_Success)
		{
			((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxMessageBox(g_hWlx,NULL, TEXT("Your user object could not be added to the predefined group. You may not have the required rights to do your work.\nPlease contact your system administrator."),TEXT("Qbe SAS Client"),MB_OK|MB_ICONEXCLAMATION);
		}

		nStatus = NetLocalGroupAddMembers(NULL, TEXT("Qbe SAS Users"), 3, (LPBYTE)&localgroupMemberInfo, 1);
	}
	
	return rc;
}

PWSTR DupString(PWSTR pszString)
{
  DWORD cbString;
  PWSTR pszNewString;

  cbString = (wcslen(pszString) + 1) * sizeof(WCHAR);
  pszNewString = LocalAlloc(LMEM_FIXED, cbString);
  if (pszNewString)
  {
    CopyMemory(pszNewString, pszString, cbString);
  }
  return(pszNewString);
}

int
WINAPI
WlxLoggedOutSAS (PVOID                pWlxContext,
                 DWORD                dwSasType,
                 PLUID                pAuthenticationId,
                 PSID                 pLogonSid,
                 PDWORD               pdwOptions,
                 PHANDLE              phToken,
                 PWLX_MPR_NOTIFY_INFO pMprNotifyInfo,
                 PVOID *              pProfile)
{
	TOKEN_STATISTICS userStats;
	DWORD cbStats;
	int iRet;
	PGINA_CONTEXT pgContext = (PGINA_CONTEXT) pWlxContext;

   if (dwSasType == WLX_SAS_TYPE_CTRL_ALT_DEL)
   {
	if (!phToken)
		return WLX_SAS_ACTION_NONE;

	switch (g_dwVersion)	//check winlogon version
	{
	case WLX_VERSION_1_3:
		{	
		iRet = ((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxDialogBox( g_hWlx,
			g_hSASGinaDll,
			MAKEINTRESOURCEW(IDD_SAS_LOGIN),
			NULL,
			MyLoginBoxProc);
		break;
		}
	case WLX_VERSION_1_4:
	default:
		{	
		iRet = ((PWLX_DISPATCH_VERSION_1_4) g_pWinlogon)->WlxDialogBox( g_hWlx,
			g_hSASGinaDll,
			MAKEINTRESOURCEW(IDD_SAS_LOGIN),
			NULL,
			MyLoginBoxProc);
		break;
		}
	}
	

	switch (iRet)
	{
	case QBEGINA_LOGINACTION_NONE:		// dialog says, user pressed cancel or user/pass were wrong
		iRet = WLX_SAS_ACTION_NONE;
		break;
	case QBEGINA_LOGINACTION_LOGON:		// dialog says, user pressed ok and user/pass were ok

		
		
		if (!phToken)
			return WLX_SAS_ACTION_NONE;
		
		// set auto admin logon
	//	qbe_registry_autologonuser(g_AccountInfo.pszUsername, g_AccountInfo.pszPassword);
		
		g_AccountInfo.bCreatedLocalUser = FALSE;
		if (g_HostPolicy->enableDynamicUser)
		{
			if (qbe_sam_createuser(g_AccountInfo.pszUsername, g_AccountInfo.pszPassword) == 0)
				g_AccountInfo.bCreatedLocalUser = TRUE;
		}

		if (!LogonUser(g_AccountInfo.pszUsername,
			L".", 
			g_AccountInfo.pszPassword,
			LOGON32_LOGON_UNLOCK,
			LOGON32_PROVIDER_DEFAULT,
			phToken))
			return WLX_SAS_ACTION_NONE;

		// Check the user token.
		if (!(*phToken)) {
			return WLX_SAS_ACTION_NONE;
		}

		// Save the user token in the GINA context
		pgContext->UserToken =*phToken;

		// Pass back null options.
		*pdwOptions = 0;
		
		
	/*	iRet = pfWlxLoggedOutSAS(pWlxContext,
			dwSasType,
			pAuthenticationId,
			pLogonSid,
			pdwOptions,
			phToken,
			pMprNotifyInfo,
			pProfile
			);
*/
		// probably DANGEROUS ?
	//	if (pProfile != NULL)
	//		HeapFree(GetProcessHeap(), 0, pProfile);
	
		{
			PWLX_PROFILE_V1_0	pWlxProfile = NULL;
			pWlxProfile = (PWLX_PROFILE_V1_0)HeapAlloc(GetProcessHeap(), 0, sizeof(WLX_PROFILE_V1_0) );
			if (pWlxProfile != NULL)
			{	
				LPWSTR szProfilePath = (LPWSTR)malloc(512);
				swprintf(szProfilePath,L"\\\\qbe-auth\\%s\\profile",g_AccountInfo.pszUsername);
				pWlxProfile->dwType = WLX_PROFILE_TYPE_V1_0;
				pWlxProfile->pszProfile = szProfilePath;
				*pProfile = pWlxProfile;
				_wmkdir(szProfilePath);
			}	
		}

		// Get the authenticationid from the user token.
		if (!GetTokenInformation(*phToken,
			TokenStatistics,
			(PVOID) &userStats,
			sizeof(TOKEN_STATISTICS),
			&cbStats))
		{
			return WLX_SAS_ACTION_NONE;
		} else {
			*pAuthenticationId = userStats.AuthenticationId;
		}

		// Pass back multiple provider information.
		pMprNotifyInfo->pszUserName = DupString(g_AccountInfo.pszUsername);
		pMprNotifyInfo->pszDomain = DupString(L"");
		pMprNotifyInfo->pszPassword = DupString(g_AccountInfo.pszPassword);
		pMprNotifyInfo->pszOldPassword = NULL;


		iRet = WLX_SAS_ACTION_LOGON;
		
		// reset auth
	//	qbe_registry_clearautologon();

		// tell QbeSvc that a new user has logged in
		qbe_qbesvc_login(g_AccountInfo.pszUsername, g_AccountInfo.pszPassword);

		/* Map the network drive specified in policy object.
		 * The bad thing is just, that we are too far away from the user session,
		 * and it just doesnt work here. QbeNP does this now :/
		 */
		
		break;
	case QBEGINA_LOGINACTION_SHUTDOWN:	// dialog says, shutdown the box
		iRet = WLX_SAS_ACTION_SHUTDOWN_POWER_OFF;
		break;
	case WLX_SAS_ACTION_SHUTDOWN:
		iRet = WLX_SAS_ACTION_SHUTDOWN_POWER_OFF;
		break;
	case WLX_SAS_ACTION_SHUTDOWN_POWER_OFF:
		iRet = WLX_SAS_ACTION_SHUTDOWN_POWER_OFF;
		break;
	case WLX_SAS_ACTION_SHUTDOWN_REBOOT:
		iRet = WLX_SAS_ACTION_SHUTDOWN_REBOOT;
		break;
	case WLX_SAS_ACTION_SHUTDOWN_SLEEP:
		iRet = WLX_SAS_ACTION_SHUTDOWN_SLEEP;
		break;
	case WLX_SAS_ACTION_SHUTDOWN_SLEEP2:
		iRet = WLX_SAS_ACTION_SHUTDOWN_SLEEP2;
		break;
	case WLX_SAS_ACTION_SHUTDOWN_HIBERNATE:
		iRet = WLX_SAS_ACTION_SHUTDOWN_HIBERNATE;
		break;
	case WLX_DLG_INPUT_TIMEOUT:		// Winlogon says, the user doesnt want to type...
		iRet = WLX_SAS_ACTION_NONE;
		break;
	case WLX_DLG_SAS:			// a Winlogon SAS event occoured
	case QBEGINA_LOGINACTION_WINLOGON:	// or user pressed "I want the f*cking windows login instead of you"
		iRet = pfWlxLoggedOutSAS(pWlxContext,
			dwSasType,
			pAuthenticationId,
			pLogonSid,
			pdwOptions,
			phToken,
			pMprNotifyInfo,
			pProfile
			);
		break;
	default:
		iRet = WLX_SAS_ACTION_NONE;
		break;
	}
					
   } else {

	iRet = pfWlxLoggedOutSAS(pWlxContext,
		dwSasType,
		pAuthenticationId,
		pLogonSid,
		pdwOptions,
		phToken,
		pMprNotifyInfo,
		pProfile
		);

   }
   return iRet;
}


BOOL
WINAPI
WlxActivateUserShell (PVOID pWlxContext,
                      PWSTR pszDesktopName,
                      PWSTR pszMprLogonScript,
                      PVOID pEnvironment)
{
   return pfWlxActivateUserShell(pWlxContext,
                                 pszDesktopName,
                                 pszMprLogonScript,
                                 pEnvironment);

}

int
WINAPI
WlxLoggedOnSAS (PVOID pWlxContext,
                DWORD dwSasType,
                PVOID pReserved)
{
/*   return pfWlxLoggedOnSAS(pWlxContext, 
                           dwSasType, 
                           pReserved); */
	if ( dwSasType != WLX_SAS_TYPE_CTRL_ALT_DEL ) {
		return( WLX_SAS_ACTION_NONE );
	}
	return WLX_SAS_ACTION_LOCK_WKSTA;
}


VOID
WINAPI
WlxDisplayLockedNotice (PVOID pWlxContext)
{
   pfWlxDisplayLockedNotice(pWlxContext);
}


BOOL
WINAPI
WlxIsLockOk (PVOID pWlxContext)
{
   return pfWlxIsLockOk(pWlxContext);
}


int
WINAPI
WlxWkstaLockedSAS (PVOID pWlxContext,
                   DWORD dwSasType)
{
   return pfWlxWkstaLockedSAS(pWlxContext, dwSasType);
}


BOOL
WINAPI
WlxIsLogoffOk (PVOID pWlxContext)
{
   return pfWlxIsLogoffOk(pWlxContext);
}

extern void sys_util_deletefolder(LPCWSTR directory);

VOID
WINAPI
WlxLogoff (PVOID pWlxContext)
{
	// first let MSGINA handle the user logout
	pfWlxLogoff(pWlxContext);

	// reset auth
	qbe_registry_clearautologon();

	// tell QbeSvc that the user has logged off
	qbe_qbesvc_logout();

	// then zap the user
	if ( (g_AccountInfo.bCreatedLocalUser) && (wcscmp(g_AccountInfo.pszUsername,TEXT("Administrator"))) )
	{
		BOOL bAborted = FALSE;
		LPWSTR szProfilePath = GlobalAlloc( GPTR, 2048 );

		WriteLogFile(TEXT("Deleting local user.\n"));

		if (szProfilePath)
			GetEnvironmentVariable( TEXT("USERPROFILE"), szProfilePath, 2048 );

		if (wcslen(szProfilePath)==0)
			swprintf(szProfilePath,TEXT("C:\\Dokumente und Einstellungen\\%s"),g_AccountInfo.pszUsername);
		
		if (NetUserDel(NULL, g_AccountInfo.pszUsername) != NERR_Success)
			((PWLX_DISPATCH_VERSION_1_3) g_pWinlogon)->WlxMessageBox(g_hWlx,NULL,TEXT("Your local user object could not be deleted.\nPlease contact your system administrator."),TEXT("Qbe SAS Client"),MB_OK|MB_ICONEXCLAMATION);

		sys_util_deletefolder(szProfilePath);
		
		swprintf(szProfilePath,TEXT("C:\\Dokumente und Einstellungen\\%s"),g_AccountInfo.pszUsername);
		sys_util_deletefolder(szProfilePath);

		swprintf(szProfilePath,TEXT("C:\\Documents and Settings\\%s"),g_AccountInfo.pszUsername);
		sys_util_deletefolder(szProfilePath);
	} else {
		WriteLogFile(TEXT("NOT Deleting local user.\n"));
	}
}


VOID
WINAPI
WlxShutdown(PVOID pWlxContext,
            DWORD ShutdownType)
{

   pfWlxShutdown(pWlxContext, ShutdownType);
}


//
// New for version 1.1
//

BOOL
WINAPI
WlxScreenSaverNotify (PVOID  pWlxContext,
                      BOOL * pSecure)
{

   return pfWlxScreenSaverNotify(pWlxContext, pSecure);
}

BOOL
WINAPI
WlxStartApplication (PVOID pWlxContext,
                     PWSTR pszDesktopName,
                     PVOID pEnvironment,
                     PWSTR pszCmdLine)
{

   return pfWlxStartApplication(pWlxContext,
                                pszDesktopName,
                                pEnvironment,
                                pszCmdLine);
}


//
// New for version 1.3
//

BOOL
WINAPI
WlxNetworkProviderLoad (PVOID                pWlxContext,
                        PWLX_MPR_NOTIFY_INFO pNprNotifyInfo)
{

   return pfWlxNetworkProviderLoad(pWlxContext, pNprNotifyInfo);
}


BOOL
WINAPI
WlxDisplayStatusMessage (PVOID pWlxContext,
                         HDESK hDesktop,
                         DWORD dwOptions,
                         PWSTR pTitle,
                         PWSTR pMessage)
{

   return pfWlxDisplayStatusMessage(pWlxContext,
                                    hDesktop,
                                    dwOptions,
                                    pTitle,
                                    pMessage);
}


BOOL
WINAPI
WlxGetStatusMessage (PVOID   pWlxContext,
                     DWORD * pdwOptions,
                     PWSTR   pMessage,
                     DWORD   dwBufferSize)
{

   return pfWlxGetStatusMessage(pWlxContext,
                                pdwOptions,
                                pMessage,
                                dwBufferSize);
}


BOOL
WINAPI
WlxRemoveStatusMessage (PVOID pWlxContext)
{

   return pfWlxRemoveStatusMessage(pWlxContext);
}
