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

#define THIS_PARAM_FILE_SOURCE PARAMCOUNT
#define THIS_PARAM_FILE_DEST PARAMCOUNT+1
#define THIS_PARAMCOUNT PARAMCOUNT+2

#define THIS_ERROR_OPENFILE 101

int filexs_rename(int argc, char** argv)
{
	if (argc<THIS_PARAMCOUNT) { printf("too less args\n"); return ERROR_ARGCOUNT; }
	
	rename( argv[THIS_PARAM_FILE_SOURCE] , argv[THIS_PARAM_FILE_DEST] );
	
	return 0;
}

/*
 * -eof-
 */

