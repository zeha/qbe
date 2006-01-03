// well if you really have it...
//#include <stdafx.h>
//
#define UNICODE
#define _UNICODE

#pragma once
#include <windows.h>
#include <winldap.h>
#include <stdio.h>
#include "qbereg.h"

extern void WriteLogFile(LPTSTR String);

int qbe_registry_autologonuser(LPWSTR szUsername, LPWSTR szPassword)
{
	HKEY hKey;
	int rc = -1;

	if (RegCreateKeyEx(HKEY_LOCAL_MACHINE,TEXT("SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion\\Winlogon"),0,NULL,REG_OPTION_NON_VOLATILE,KEY_ALL_ACCESS,NULL,&hKey,NULL) == ERROR_SUCCESS)
	{
		rc = 0;
		if ( (rc==0) && (RegSetValueEx(hKey,TEXT("AutoAdminLogon"),0,REG_SZ, (const BYTE*)(L"1"), wcslen(L"1")*sizeof(WCHAR)) != ERROR_SUCCESS))
			rc = -2;
		if ( (rc==0) && (RegSetValueEx(hKey,TEXT("DefaultUserName"),0,REG_SZ,(const BYTE*)szUsername, wcslen(szUsername)*sizeof(WCHAR)) != ERROR_SUCCESS))
			rc = -3;
		if ( (rc==0) && (RegSetValueEx(hKey,TEXT("DefaultPassword"),0,REG_SZ, (const BYTE*)szPassword, wcslen(szPassword)*sizeof(WCHAR)) != ERROR_SUCCESS))
			rc = -4;
		if ( (rc==0) && (RegSetValueEx(hKey,TEXT("ForceAutoLogon"),0,REG_SZ, (const BYTE*)(L"1"), wcslen(L"1")*sizeof(WCHAR)) != ERROR_SUCCESS))
			rc = -5;

		// ch:
		// I dont change the DefaultDomain here, so one can 
		// pre-set it in the registry. This makes life a bit
		// easier for those who want a user-auth to another
		// domain too. But I don't know if this works.
		//
		// Also it makes life easier for me, cause I don't
		// have to read the computer name :>
	}
	
	RegFlushKey(hKey);
	RegCloseKey(hKey);
	return rc;
}

int qbe_registry_clearautologon()
{
	HKEY hKey;
	int rc = -1;

	if (RegCreateKeyEx(HKEY_LOCAL_MACHINE,TEXT("SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion\\Winlogon"),0,NULL,REG_OPTION_NON_VOLATILE,KEY_ALL_ACCESS,NULL,&hKey,NULL) == ERROR_SUCCESS)
	{
		rc = 0;
		RegSetValueEx(hKey,TEXT("AutoAdminLogon"),0,REG_SZ, (const BYTE*)(L"0"), wcslen(L"0")*sizeof(WCHAR));
		RegDeleteValue( hKey, TEXT("DefaultUserName") );
		RegDeleteValue( hKey, TEXT("DefaultPassword") );
		RegDeleteValue( hKey, TEXT("ForceAutoLogon") );
	}
	
	RegFlushKey(hKey);
	RegCloseKey(hKey);
	return rc;
}


