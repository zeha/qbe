#include "stdafx.h"
#pragma hdrstop

int qbe_sam_createuser(LPWSTR szUsername, LPWSTR szPassword)
{
	// for the user object
	USER_INFO_2 ui;
	NET_API_STATUS nStatus;

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
	ui.usri2_comment = L"Qbe SAS User";
	ui.usri2_priv = USER_PRIV_USER;
	ui.usri2_flags = UF_PASSWD_CANT_CHANGE|UF_DONT_EXPIRE_PASSWD|UF_NORMAL_ACCOUNT;
	ui.usri2_logon_server = L"qbe-auth";

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
	if ( nStatus == NERR_UserExists )
	{
		// delete user and then recreate it
		NetUserDel(NULL, szUsername);
		nStatus = NetUserAdd(NULL, 1, (LPBYTE)&ui, NULL);
		if (nStatus == NERR_UserExists)
			rc = -2;	// cant create user
	}


	if (rc==0)
	{
		// add to admin group
		localgroupMemberInfo.lgrmi3_domainandname = szUsername;

		AllocateAndInitializeSid(&sidNTAuthority, 2, SECURITY_BUILTIN_DOMAIN_RID, DOMAIN_ALIAS_RID_ADMINS, 0, 0, 0, 0, 0, 0, &pSidAdmins);
		LookupAccountSidW(NULL, pSidAdmins, szAdminGroupName, &ulAdminGroupName, szLocalDomainName, &ulLocalDomainName, &sidNameUse);

		nStatus = NetLocalGroupAddMembers(NULL, szAdminGroupName, 3, (LPBYTE)&localgroupMemberInfo, 1);
		if (nStatus != NERR_Success)
		{
			rc = -3;
		}
	}
	return rc;
}

int qbe_sam_deleteuser(LPWSTR szUsername)
{
	// for the user object
	USER_INFO_2 ui;
	NET_API_STATUS nStatus = NERR_Success;

	int rc = 0;		// we pretend we are ok :>
	
	ui.usri2_name = szUsername;
	ui.usri2_priv = USER_PRIV_USER;
	
	// delete user and then recreate it
	NetUserDel(NULL, szUsername);
	if (nStatus != NERR_Success)
		rc = -2;	// cant delete user

	return rc;
}
