// well if you really have it...
//#include <stdafx.h>
//
#define UNICODE
#define _UNICODE

#pragma once
#pragma warning ( push )
#pragma warning ( disable : 4005 )
#include <windows.h>
#include <winldap.h>
#include <stdio.h>
#include <tchar.h>
#pragma warning ( pop )
#include "qbeldap.h"

#ifndef HAVE_WRITELOGFILE
extern void WriteLogFile(LPTSTR String);
#endif

/// UEberprueft Benutzername + Passwort gegenueber dem LDAP-Server
int qbe_ldap_checkuser(LPWSTR username, LPWSTR password)
{
	int rc = -1;
	LDAP* ld;
	LONG lv = 0;
	LPWSTR szSearch = (PWSTR)malloc(((unsigned int)wcslen(username))+1024);
	LPWSTR szDN = (PWSTR)malloc(((unsigned int)wcslen(username))+1024);
	LDAPMessage *pMsg = NULL;

	// well, ldap doesnt accept empty user/pass
	// so fuck him if he tries it...
	if ((!wcslen(username)) || (!wcslen(password)))
		return -10;

	// and, ehm, he shouldnt fuck us with the 1024 chars and so
	if ((wcslen(username)>1000) || (wcslen(password)>1000))
		return -10;


	ld = ldap_sslinit(QBE_LDAP_SERVER,LDAP_SSL_PORT,1);
//	ld = ldap_init(QBE_LDAP_SERVER, QBE_LDAP_PORT);
	if (ld==NULL)
		return -2;	// server failure

	// check if ssl enabled...?
	rc = ldap_get_option(ld,LDAP_OPT_SSL,(void*)&lv);
	if (rc != LDAP_SUCCESS)
		return -2;

	// If SSL is not enabled, enable it.
	if ((void*)lv != LDAP_OPT_ON)
	{	// no! damn! enable that.
		rc = ldap_set_option(ld,LDAP_OPT_SSL,LDAP_OPT_ON);
		if (rc != LDAP_SUCCESS)
			return -2;
	}

	
	if (ldap_connect(ld, NULL) != LDAP_SUCCESS)
		return -2;	// server failure

	ldap_simple_bind_s(ld, TEXT(""), TEXT(""));	// anonymous bind

	swprintf(szSearch, QBE_LDAP_SEARCH, username);
	if (ldap_search_s(ld, QBE_LDAP_BASE, LDAP_SCOPE_SUBTREE, szSearch, NULL, FALSE, &pMsg) == LDAP_SUCCESS)
	{
		// ok user exists
		// lets see what to do next...ehm...login? ;>

		//TCHAR szTemp[1024];
		PWCHAR DN;
		LDAPMessage *pEntry = ldap_first_entry(ld, pMsg);
		DN = ldap_get_dn(ld, pEntry);
		if (DN == NULL)
			rc = -10;
		else if (!wcslen(DN))
			rc = -10;

		// so far so good
		// just hope that the fucker supplied a valid username
		// and ehm see...
		if (rc==-1)
		{
			// ok i got a username above
			// now fuck our old connection and create a new one
			ldap_unbind(ld);
			ld = ldap_init(QBE_LDAP_SERVER, QBE_LDAP_PORT);
			ldap_connect(ld, NULL);
			// just for logging in...
			if (ldap_simple_bind_s(ld, DN, password) == LDAP_SUCCESS)	// the real bind
				rc = 0;			// ok
			else
				rc = -11;		// wrong password, sucker.
		}

		// m$ wants you to clear all up after they were here
		if (DN != NULL)
			ldap_memfree(DN);

	} else {
		rc = -10;	// user invalid
	}

	// zap that dog out!
	if (pMsg != NULL)
		ldap_msgfree(pMsg);

	// and yes...kill that stale connection :>
	ldap_unbind(ld);
	return rc;	// ldap error
}

int qbe_ldap_getpolicy( LPWSTR szWorkstationName, struct QbeSAS_HostPolicy *policy )
{
	int rc = -1;
	LDAP* ld;
	LPWSTR szPolicyName;
	LPWSTR szSearch = (LPWSTR)malloc(((unsigned int)wcslen(szWorkstationName))+1024);
	LPWSTR szDN = (LPWSTR)malloc(((unsigned int)wcslen(szWorkstationName))+1024);
	LDAPMessage *pMsg = NULL;

	policy->enableDynamicUser = 0;
	policy->enableDynamicUser = 0;
	policy->DynamicUserGroup = NULL;
	policy->LoginScript = NULL;
	policy->HomeDrive = NULL;
	policy->HomeDriveDir = NULL;

	ld = ldap_init(QBE_LDAP_SERVER, QBE_LDAP_PORT);
	if (ld==NULL) 
		return -2;	// server failure
	if (ldap_connect(ld, NULL) != LDAP_SUCCESS)
		return -2;	// server failure
	ldap_simple_bind_s(ld, TEXT(""), TEXT(""));	// anonymous bind

	swprintf(szSearch, QBE_LDAP_HOSTSEARCH, szWorkstationName);
	if (ldap_search_s(ld, QBE_LDAP_BASE, LDAP_SCOPE_SUBTREE, szSearch, NULL, FALSE, &pMsg) == LDAP_SUCCESS)
	{
		// ok host exists
		// lets see what to do next...

		PWCHAR DN;
		LDAPMessage *pEntry;

		pEntry = ldap_first_entry(ld, pMsg);
		DN = ldap_get_dn(ld, pEntry);
		if (DN != NULL)
		{
			if (!wcslen(DN))
				rc = -10;
		} else {
			rc = -10;
		}
		

		// so far so good
		// just hope that the fucker supplied a valid username
		// and ehm see...
		if (rc==-1)
		{
			// get get get a policy dn
			// ...
			PWCHAR* pValues;
			
			ldap_memfree(DN); DN = NULL;

			pValues = ldap_get_values(ld, pEntry, TEXT("qbePolicyName"));
			if (!ldap_count_values(pValues))
			{
				// doh! no values :/
				rc = -11;
WriteLogFile(TEXT("No policy name in host object.\r\n"));
			} else {
WriteLogFile(TEXT("Host object references policy name:\r\n"));
				// fine, very fine.
				szPolicyName = (LPWSTR)malloc(((unsigned int)wcslen(*pValues))+1024);
				wcscpy(szPolicyName,*pValues);
				ldap_value_free(pValues);
WriteLogFile(szPolicyName);
WriteLogFile(TEXT("\r\n"));
			}
			
			
		}

	
		// free that.
		ldap_msgfree(pMsg);
		pMsg = NULL;

	} else {
		rc = -10;	// host invalid
	}

	if (rc==-1)
	{
		// ok i have a policy-dn in policyName.
		// 
		if (ldap_search_s(ld, szPolicyName, LDAP_SCOPE_BASE, TEXT("(objectClass=*)"), NULL, FALSE, &pMsg) == LDAP_SUCCESS)
		{	// search ok
			PWCHAR* pValues;
			LDAPMessage *pEntry;
			
			pEntry = ldap_first_entry(ld, pMsg);
			if (pEntry == NULL)
				rc = -12;	// unable to read policy object

			if (rc==-1)
			{			
				pValues = ldap_get_values(ld, pEntry, TEXT("qbePolicyDynamicUserEnabled"));
				if (!ldap_count_values(pValues))
				{
					policy->enableDynamicUser = 0;
				} else {
					policy->enableDynamicUser = (*pValues[0]=='1');
					ldap_value_free(pValues);
				}
			
				pValues = ldap_get_values(ld, pEntry, TEXT("qbePolicyDynamicUserGroup"));
				if (!ldap_count_values(pValues))
				{
					policy->DynamicUserGroup = NULL;
				} else {
					policy->DynamicUserGroup = (LPWSTR)malloc(((unsigned int)wcslen(*pValues))+1024);
					wcscpy(policy->DynamicUserGroup,*pValues);
					ldap_value_free(pValues);
				}

				pValues = ldap_get_values(ld, pEntry, TEXT("qbePolicyLoginScript"));
				if (!ldap_count_values(pValues))
				{
					policy->LoginScript = NULL;
				} else {
					policy->LoginScript = (LPWSTR)malloc(((unsigned int)wcslen(*pValues))+1024);
					wcscpy(policy->LoginScript,*pValues);
					ldap_value_free(pValues);
				}

				pValues = ldap_get_values(ld, pEntry, TEXT("qbePolicyHomeDrive"));
				if (!ldap_count_values(pValues))
				{
					policy->HomeDrive = NULL;
				} else {
					policy->HomeDrive = (LPWSTR)malloc(((unsigned int)wcslen(*pValues))+1024);
					wcscpy(policy->HomeDrive,*pValues);
					ldap_value_free(pValues);
				}

				pValues = ldap_get_values(ld, pEntry, TEXT("qbePolicyHomeDriveDir"));
				if (!ldap_count_values(pValues))
				{
					policy->HomeDriveDir = NULL;
				} else {
					policy->HomeDriveDir = (LPWSTR)malloc(((unsigned int)wcslen(*pValues))+1024);
					wcscpy(policy->HomeDriveDir,*pValues);
					ldap_value_free(pValues);
				}
			}
			
		} else {
			// didnt find cool policy object
			rc = -12;
		}
	}


	// zap that dog out!
	if (pMsg != NULL)
		ldap_msgfree(pMsg);

	// and yes...kill that stale connection :>
	ldap_unbind(ld);
	return rc;	// ldap error
}
