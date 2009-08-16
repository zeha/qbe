#include <stdio.h>

int main(int argc, char** argv)
{
	char uid[100];
	char commandline[150];
	fprintf(stderr,"sasdu 1.0 (c) Copyright 2003 Christian Hofstaedtler\n");
	if (argc != 2)
	{
		fprintf(stderr,"sasdu: wrong call, use sasdu username!\n");
		exit(-1);
	}

	strncpy(uid,argv[1],98);	uid[98]=0;
	sprintf(commandline,"du -sxh /import/homes/%s",uid);

	printf("calling %s\n",commandline);
	system(commandline);
}

