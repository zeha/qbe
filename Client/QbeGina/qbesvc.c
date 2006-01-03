
#define UNICODE

#include <windows.h>
#include <winhttp.h>

#include "qbesvc.h"
void WriteLogFile(LPTSTR String);

/// UEbergibt szUsername und szPassword via HTTP an den lokalen QbeSvc
DWORD qbe_qbesvc_login(LPWSTR szUsername, LPWSTR szPassword)
{
	LPWSTR lpUrl;
	DWORD dwSecSize;
	DWORD dwSecData;
	DWORD dFlags;
	HINTERNET myConn;
	HINTERNET myReq;
	HINTERNET myInet;
       	DWORD rc = NO_ERROR;

	myInet = WinHttpOpen( TEXT("User-Agent: QbeNP/2.23"),
                          WINHTTP_ACCESS_TYPE_NO_PROXY,
                          WINHTTP_NO_PROXY_NAME, 
                          WINHTTP_NO_PROXY_BYPASS, 0 );
	
	lpUrl = LocalAlloc(LPTR,1024);
	
	dFlags = WINHTTP_FLAG_REFRESH;
	dwSecSize = sizeof(DWORD);
	dwSecData = SECURITY_FLAG_IGNORE_CERT_CN_INVALID | SECURITY_FLAG_IGNORE_CERT_DATE_INVALID | SECURITY_FLAG_IGNORE_UNKNOWN_CA;

	myConn = WinHttpConnect( myInet, TEXT("localhost"), 7666, 0 );
	if (myConn == NULL)
	{
		rc = WN_NO_NETWORK;
		goto done;
	}

	wsprintf(lpUrl,TEXT("/auth/setlogin?user=%s&pass=%s&source=gina"),szUsername,szPassword);
WriteLogFile(lpUrl);
WriteLogFile(TEXT("\r\n"));
	myReq = WinHttpOpenRequest( myConn, L"GET", lpUrl, NULL, WINHTTP_NO_REFERER, WINHTTP_DEFAULT_ACCEPT_TYPES, dFlags );
	WinHttpSetOption( myReq, WINHTTP_OPTION_SECURITY_FLAGS, &dwSecData, dwSecSize );
	
	if (WinHttpSendRequest( myReq, WINHTTP_NO_ADDITIONAL_HEADERS, 0, WINHTTP_NO_REQUEST_DATA, 0, 0, 0 ) == FALSE)
	{
		rc = WN_NO_NETWORK;
		goto done;
	}	
	
	WinHttpReceiveResponse( myReq, NULL);

	if (myReq)	WinHttpCloseHandle(myReq);

	myReq = WinHttpOpenRequest( myConn, TEXT("GET"), TEXT("/auth/login"), NULL, WINHTTP_NO_REFERER, WINHTTP_DEFAULT_ACCEPT_TYPES, dFlags );
	WinHttpSetOption( myReq, WINHTTP_OPTION_SECURITY_FLAGS, &dwSecData, dwSecSize );
	
	WinHttpSendRequest( myReq, WINHTTP_NO_ADDITIONAL_HEADERS, 0, WINHTTP_NO_REQUEST_DATA, 0, 0, 0 );
	WinHttpReceiveResponse( myReq, NULL);

done:
	if (myReq)	WinHttpCloseHandle(myReq);
	if (myConn)	WinHttpCloseHandle(myConn);
	if (myInet)	WinHttpCloseHandle(myInet);

	LocalFree(lpUrl);

	return rc;
}

/// Initiiert im QbeSvc die Abmeldung des aktiven Benutzers
DWORD qbe_qbesvc_logout()
{
	DWORD dwSecSize;
	DWORD dwSecData;
	DWORD dFlags;
	HINTERNET myConn;
	HINTERNET myReq;
	HINTERNET myInet;
       	DWORD rc = NO_ERROR;

	myInet = WinHttpOpen( TEXT("User-Agent: QbeNP/2.23"),  
                          WINHTTP_ACCESS_TYPE_NO_PROXY,
                          WINHTTP_NO_PROXY_NAME, 
                          WINHTTP_NO_PROXY_BYPASS, 0 );
	
	dFlags = WINHTTP_FLAG_REFRESH;
	dwSecSize = sizeof(DWORD);
	dwSecData = SECURITY_FLAG_IGNORE_CERT_CN_INVALID | SECURITY_FLAG_IGNORE_CERT_DATE_INVALID | SECURITY_FLAG_IGNORE_UNKNOWN_CA;

	myConn = WinHttpConnect( myInet, TEXT("localhost"), 7666, 0 );
	if (myConn == NULL)
	{
		rc = WN_NO_NETWORK;
		goto done;
	}

	myReq = WinHttpOpenRequest( myConn, TEXT("GET"), TEXT("/auth/logout"), NULL, WINHTTP_NO_REFERER, WINHTTP_DEFAULT_ACCEPT_TYPES, dFlags );
	WinHttpSetOption( myReq, WINHTTP_OPTION_SECURITY_FLAGS, &dwSecData, dwSecSize );
	
	WinHttpSendRequest( myReq, WINHTTP_NO_ADDITIONAL_HEADERS, 0, WINHTTP_NO_REQUEST_DATA, 0, 0, 0 );
	WinHttpReceiveResponse( myReq, NULL);

done:
	if (myReq)	WinHttpCloseHandle(myReq);
	if (myConn)	WinHttpCloseHandle(myConn);
	if (myInet)	WinHttpCloseHandle(myInet);

	return rc;
}
