/*
 * Qbe FileXS Helper 
 * 
 * (C) Copyright 2003 Christian Hofstaedtler
 *
 */

#include <sys/types.h>
#include <stdio.h>
#include <unistd.h>
#include <errno.h>
#include <stdlib.h>

#include "filexs.h"

#define THIS_PARAM_FILE PARAMCOUNT
#define THIS_PARAMCOUNT PARAMCOUNT+1

#define THIS_ERROR_OPENFILE 101

int filexs_fileget(int argc, char** argv)
{
	int rc;
	FILE* fFile = NULL;
	char filebuf[1001];

	if (argc<THIS_PARAMCOUNT) { printf("too less args\n"); return ERROR_ARGCOUNT; }
	
	fFile = fopen( argv[THIS_PARAM_FILE] ,"r");
	if (fFile == NULL) { printf("cant open file\n"); return THIS_ERROR_OPENFILE; }

	rc = -1;
	while ((rc = fread(filebuf,1,1000,fFile)) > 0)
	{
		fwrite(filebuf,1,rc,stdout);

		rc = -1;
	}
	
	fclose(fFile);
	
	return ERROR_SUCCESS;
}

/*
 * -eof-
 */

