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

void WriteLogFile(LPTSTR String)
{
	printf("log: %s\n",String);
}

int main()
{
	int rc = qbe_ldap_checkuser(L"e99071", L"fooo");
	printf("rc: %d\n",rc);
}

// *eof*

