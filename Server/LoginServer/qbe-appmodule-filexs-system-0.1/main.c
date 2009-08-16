/*
 * Qbe FileXS Helper Main 
 * 
 * (C) Copyright 2003 Christian Hofstaedtler
 *
 */

#include <sys/types.h>
#include <stdio.h>
#include <unistd.h>
#include <errno.h>
#include <stdlib.h>
#include <pwd.h>
#include <grp.h>
#include <string.h>

#include "filexs.h"

int main(int argc, char** argv)
{
	struct passwd* pw;
	struct group* gr;

	if (argc<PARAMCOUNT) { printf("too less args\n"); return ERROR_ARGCOUNT; }
	
	pw = getpwnam(argv[PARAM_USER]);
	if (pw == NULL) { printf("unknown user\n"); return 2; }
	if (pw->pw_uid < ADMIN_UID) { printf("not doing admin\n"); return ERROR_USERGROUP; }
	setuid(pw->pw_uid);

	if (argv[PARAM_GROUP][0] == '-')
	{
		if (pw->pw_gid < ADMIN_GID) { printf("not doing admin group\n"); return ERROR_USERGROUP; }
		setgid(pw->pw_gid);
	} else {
		gr = getgrnam(argv[PARAM_USER]);
		if (gr == NULL) { printf("unknown group\n"); return ERROR_USERGROUP; }
		if (gr->gr_gid < ADMIN_GID) { printf("not doing admin group\n"); return ERROR_USERGROUP; }
		setgid(gr->gr_gid);
	}

	// fileget
	if (!strcmp(argv[PARAM_ACTION],ACTION_FILEGET))
		return filexs_fileget(argc,argv);
	// fileput
	//if (!strcmp(argv[PARAM_ACTION],ACTION_FILEPUT))
	//	return filexs_fileput(argc,argv);
	
	// delete
	if (!strcmp(argv[PARAM_ACTION],ACTION_DELETE))
		return filexs_delete(argc,argv);
	
	// rename
	if (!strcmp(argv[PARAM_ACTION],ACTION_RENAME))
		return filexs_rename(argc,argv);

	// mkdir
	if (!strcmp(argv[PARAM_ACTION],ACTION_MKDIR))
		return filexs_mkdir(argc,argv);
	
	// rmdir
	if (!strcmp(argv[PARAM_ACTION],ACTION_RMDIR))
		return filexs_rmdir(argc,argv);


}

/*
 * -eof-
 */

