// stdafx.h : Includedatei f�r Standardsystem-Includedateien,
// oder h�ufig verwendete, projektspezifische Includedateien,
// die nur in unregelm��igen Abst�nden ge�ndert werden.

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
