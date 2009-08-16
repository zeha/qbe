//////////////////////////////////////////////////
//                                              //
//  SAS WriteUserNote helper program            //
//                                              //
//  (C) Copyright 2002 Christian Hofstaedtler   //
//                                              //
//  This program is part of the SAS package,    //
//  and may only be distributed as part of      //
//  the SAS package and under the same license  //
//  as the SAS package.                         //
//                                              //
//////////////////////////////////////////////////

#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <math.h>
#include <malloc.h>
#include <string.h>
#include <memory.h>
#include <pwd.h>
/*
#define uidNumber	argv[1]
#define gidNumber	argv[2]
#define uidText		argv[3]
#define opCode		argv[4]
*/
#define uidText		argv[1]
#define opCode		argv[2]

int main(int argc, char** argv)
{
	FILE* fp;
	int userid;
	int groupid;
	char* notefile;
	char* noteText;
	struct passwd *pw = (struct passwd*)malloc(sizeof(struct passwd));
	
	printf("\nSAS/WriteUserNote helper program 0.01");
	printf("\nCopyright 2002 Christian Hofstaedtler\n\n");
	if (argc < 2 /*4*/)
	{	fprintf(stderr," Incorrect Parameter Count.\n");
		fprintf(stderr," Usage: sasusernote uid [-kill]\n");
		return -1;
	}
	
/*	// set group id
	groupid = atoi(gidNumber);
	if ((groupid < 100) || (groupid > 65665))
	{	fprintf(stderr," SAS won't specify a group id < 100 ...\n"); return -2; }
i*/
	pw = getpwnam(uidText);
	userid = pw->pw_uid;
	groupid = pw->pw_gid;
	setgid(groupid);
	
/*	// set user id
	userid = atoi(uidNumber);
	if ((userid < 100) || (userid > 65665))
	{	fprintf(stderr," SAS won't specify a user id < 100 ...\n"); return -2; }
*/	setuid(userid);

	
	printf(" User/Group ID: %d/%d\n",getuid(),getgid());

	notefile = (char*)malloc(strlen((unsigned char*)uidText)+30);
	if (notefile == NULL)
	{	fprintf(stderr," Out of memory!?!\n");	return -3; 	}
	sprintf(notefile,"/import/homes/%s/.sasnotes",uidText);

	if (argc == 3)
	if (memcmp(opCode,"-kill",5) == 0)
	{
		printf(" Removing SAS Usernote file!\n");
		unlink(notefile);
		return 0;
	}

	fp = fopen(notefile,"a");
	free(notefile);
	
	if (fp == NULL)
	{	fprintf(stderr," Error opening .sasnotes file!\n");	return -4;	}
	
	noteText = (char*)malloc(40000);
	if (noteText == NULL)
	{	fprintf(stderr," Out of memory!?!\n");	return -3; 	}
	
	printf(" Enter Text:\n");
	do {
		noteText[0] = 0;
		fgets(noteText,39999,stdin);
		noteText[39999] = 0;
		
		if (noteText[0] == '.')
		{	noteText[0] = 0;
			break;
		}
		fprintf(fp,"%s",noteText);	
	} while (noteText[0] != '.');
	
	fprintf(fp,".\n");
	fclose(fp);

	return 0;
}
