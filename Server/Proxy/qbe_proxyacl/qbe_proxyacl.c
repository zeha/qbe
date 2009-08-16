/* qbe_proxyacl.c : Copyright 2002-2003 Christian Hofstaedtler
   $Id: qbe_proxyacl.c,v 1.10 2003/03/20 11:59:33 ch Exp $
 */

#include "qbe_proxyacl.h"

aclEntry ACLTable[TABLESIZE+1];
/*#undef NDEBUG*/
#define linesize 900
char url[256];
char method[32];
char username[256];
char ip[256];
char line[linesize];
FILE* TableDBFile;
 
#include <sys/types.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <signal.h>
#define _PATH_PROCNET_ARP "/proc/net/arp"

void readTableDB();

void sighandler(int sig)
{
	readTableDB();
	signal(SIGHUP,sighandler);
}

char* arp_getmac(char *searchip)
{
    char ip[100];
    char hwa[100];
    char mask[100];
    char line[200];
    char dev[100];
    char* founddev;
    int type, flags;
    FILE *fp;
    int num; 
    founddev = NULL;
    
    /* Open the PROCps kernel table. */
    if ((fp = fopen(_PATH_PROCNET_ARP, "r")) == NULL) {
	perror(_PATH_PROCNET_ARP);
	return "";
    }
    if (fgets(line, sizeof(line), fp) != (char *) NULL) {
	/* Read the ARP cache entries. */
	for (; fgets(line, sizeof(line), fp);) {
	    num = sscanf(line, "%s 0x%x 0x%x %100s %100s %100s\n",
			 ip, &type, &flags, hwa, mask, dev);
	    if (num < 4)
		break;
#ifndef NDEBUG	   
	    printf("'%s'='%s' ",ip,searchip);
#endif
	    
	    if (ip[0] && strcmp(ip, searchip))
		continue;

	    founddev = strdup(hwa);
	    break;
	}
    }
    fclose(fp);
    return founddev == NULL ? "" : founddev;
}

void readTableDB()
{
#ifndef NDEBUG
	printf("(Re-)Reading Data Table\n");
#endif
	fread( &ACLTable, sizeof(aclEntry), TABLESIZE, TableDBFile );
}

int main(int argc, char* argv[])
{
	int entry;
	int done;
	char* checkmac;
	FILE* log;
	char* cp;
	char* aclUser;

	// line buffered stdout
	setvbuf(stdout, NULL, _IOLBF, 0);
	// change to / so we dont get into troubles when something gets umounted
	chdir("/");
	// install a signal handler for reloading the config
	signal(SIGHUP,sighandler);
	
	done = 0;
	if (argc > 1)
	{
		printf("Qbe systems: qbe_proxyacl 0.01 for squid2.4\n");
		printf("(C) Copyright 2002-2003 Christian Hofstaedtler\n");
	} else {
		TableDBFile = fopen(TABLEPATH,"rb");
		if (TableDBFile == NULL)
		{	printf("ERR");
			return -1;
		}
		readTableDB();
		

#ifdef WITH_LOG
		sprintf(url,"/tmp/proxyacl-log-%d.log",getpid());
		log = fopen(url,"wt");
		setvbuf(log, NULL, _IOLBF, 0);
#endif
		
		while (fgets(line,linesize,stdin))
		{
			done = 0;
			url[0] = 0;
			ip[0] = 0;
			username[0] = 0;
			method[0] = 0;
			aclUser = "";

			sscanf(line, "%255s %255s %255s %31s\n", url, ip, username, method);
			if ((cp = strchr (ip, '/')) != NULL)
			{	*cp = '\0';	}
#ifdef WITH_LOG
			fprintf(log, "%s %s %s %s\n", url, ip, username, method);
#endif
/*		
			
			if ((cp = strchr (input, '\n')) != NULL) {
				*cp = '\0';
			}
			if ((cp = strtok (input, " \t")) != NULL) {   
				strncpy(ip, cp, 16);*/
				checkmac = arp_getmac(ip);
/*			} else {
				printf("ERR\n");
				continue;
			}
			*/
// http://www.void.at/ 10.0.1.11 - GET		
			
#ifndef NDEBUG
			fprintf(stderr,"<- user: '%s', ip: '%s', mac: '%s'\n",username,ip,checkmac);
#endif
			for (entry=0;entry<TABLESIZE;entry++)
			{
#ifndef NDEBUG
				fprintf(stderr,"-> user: '%s', ip: '%s', mac: '%s' ",ACLTable[entry].aclUsername,ACLTable[entry].aclIp,ACLTable[entry].aclMac);
#endif
				if (ACLTable[entry].aclIp[0] == 0)
					break;
				
				if (!strcmp(ACLTable[entry].aclIp,ip))
				{
					aclUser = ACLTable[entry].aclUsername;
#ifndef NDEBUG
					fprintf(stderr,"--> ip ok\n");
#endif
					if ( 
						(!strcmp(checkmac,ACLTable[entry].aclMac)) || 
						(ACLTable[entry].aclMac[0]=='-') || 
						(ACLTable[entry].aclMac[0]==0) 
					   )
					{
						/*printf( "OK\n" );*/
						printf("%s\n",url);
						done=1;
						break;
					}
				}
			}	
			
			if (done==0)
				printf("http://blackbox.htlwrn.ac.at/rpc/proxy-403.php?ip=%s&mac=%s&acluid=%s\n",ip,checkmac,aclUser);
			/*	printf( "ERR\n" );*/

		} /* end of while */
		
		fclose(TableDBFile);
#ifdef WITH_LOG
		fclose(log);
#endif
	}

	exit(EXIT_SUCCESS);
}
