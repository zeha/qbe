// stdafx.h : Includedatei für Standardsystem-Includedateien,
// oder häufig verwendete, projektspezifische Includedateien,
// die nur in unregelmäßigen Abständen geändert werden.

#pragma once

#include <string.h>
#include <stdio.h>
#include <windows.h>

#include <lm.h>
#include <tchar.h>
#include <direct.h>
#include <winnetwk.h>
#include <time.h>

extern int qbe_sam_createuser(LPWSTR szUsername, LPWSTR szPassword);
extern int qbe_sam_deleteuser(LPWSTR szUsername);
extern int qbe_nt_launchapp(char* user, char* pass, char* app);
