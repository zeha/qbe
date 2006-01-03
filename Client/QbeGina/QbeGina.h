/*
 * $Id: QbeGina.h 71 2004-05-03 06:52:31Z ch $
 * 
 * (C) Copyright 2002-2004 Christian Hofstaedtler
 * (C) Copyright 1996-2000 Microsoft Corporation
 */

//
// Function prototypes for the GINA interface.
//

typedef BOOL (WINAPI * PFWLXNEGOTIATE)  (DWORD, DWORD *);
typedef BOOL (WINAPI * PFWLXINITIALIZE) (LPWSTR, HANDLE, PVOID, PVOID, PVOID *);
typedef VOID (WINAPI * PFWLXDISPLAYSASNOTICE) (PVOID);
typedef int  (WINAPI * PFWLXLOGGEDOUTSAS) (PVOID, DWORD, PLUID, PSID, PDWORD,
                                           PHANDLE, PWLX_MPR_NOTIFY_INFO, 
                                           PVOID *);
typedef BOOL (WINAPI * PFWLXACTIVATEUSERSHELL) (PVOID, PWSTR, PWSTR, PVOID);
typedef int  (WINAPI * PFWLXLOGGEDONSAS) (PVOID, DWORD, PVOID);
typedef VOID (WINAPI * PFWLXDISPLAYLOCKEDNOTICE) (PVOID);
typedef int  (WINAPI * PFWLXWKSTALOCKEDSAS) (PVOID, DWORD);
typedef BOOL (WINAPI * PFWLXISLOCKOK) (PVOID);
typedef BOOL (WINAPI * PFWLXISLOGOFFOK) (PVOID);
typedef VOID (WINAPI * PFWLXLOGOFF) (PVOID);
typedef VOID (WINAPI * PFWLXSHUTDOWN) (PVOID, DWORD);

//
// New for version 1.1
//

typedef BOOL (WINAPI * PFWLXSCREENSAVERNOTIFY) (PVOID, BOOL *);
typedef BOOL (WINAPI * PFWLXSTARTAPPLICATION) (PVOID, PWSTR, PVOID, PWSTR);

//
// New for version 1.3
//

typedef BOOL (WINAPI * PFWLXNETWORKPROVIDERLOAD) (PVOID, PWLX_MPR_NOTIFY_INFO);
typedef BOOL (WINAPI * PFWLXDISPLAYSTATUSMESSAGE) (PVOID, HDESK, DWORD, PWSTR, PWSTR);
typedef BOOL (WINAPI * PFWLXGETSTATUSMESSAGE) (PVOID, DWORD *, PWSTR, DWORD);
typedef BOOL (WINAPI * PFWLXREMOVESTATUSMESSAGE) (PVOID);

typedef struct {
/// Benutzername
  LPWSTR                   pszUsername;
/// Domain-/Computername
  LPWSTR                   pszDomain;
/// Passwort
  LPWSTR                   pszPassword;
/// Vollständiger Name
  LPWSTR                   pszFullname;
/// User-ID-Nummer
  LPWSTR                   pszUID;
/// Gruppen-ID-Nummer
  LPWSTR                   pszGID;
/// Token
  HANDLE                  hUserToken;
/// Am Server angemeldet?
  BOOL                    bServerAuth;
/// Lokalen Benutzer erstellt?
  BOOL                    bCreatedLocalUser;
} QbeGina_AccountInfo, *PQbeGina_AccountInfo;

typedef struct {
  HANDLE hWlx;
  LPWSTR station;
  PWLX_DISPATCH_VERSION_1_3 pWlxFuncs;
  HANDLE hDllInstance;
  HANDLE UserToken;
} GINA_CONTEXT, *PGINA_CONTEXT;

QbeGina_AccountInfo g_AccountInfo;

