/*
 * SAS daemon
 *
 * (c) Copyright 2002, 2003 Christian Hofstaedtler
 *
 */

#include <sys/types.h>
#include <stdio.h>
#include <dirent.h>
#include <unistd.h>
#include <errno.h>
#include <stdlib.h>
#include <syslog.h>
#define MAX_BUF		2000
#define MAX_PATH	MAX_BUF

int sas_process_activations(void)
{
	DIR* d;
	FILE* fp;
	struct dirent *entry;
	char callbuf[MAX_PATH];

	chdir("/qbe/status/activation/user/");

	d = opendir("/qbe/status/activation/user/");
	if (d == NULL)
	{	
		syslog(LOG_CRIT,"could not change to activation directory");
		switch (errno)
		{
			case ENOTDIR:
			case ENOENT:
				syslog(LOG_CRIT,"could not access activation directory\n");
				return -2;
				break;
			case ENOMEM:
			case ENFILE:
			case EMFILE:
				break;
			default:
				break;
		}
	} else {

		do {
			entry = readdir(d); //entry = readdir(d);
			if (entry != NULL)
			{	
				/* ignore hidden files */
				if (entry->d_name[0] != '.')
				{
	syslog(LOG_NOTICE,"Processing activation for user %s",entry->d_name);
	
					fp = fopen(entry->d_name,"rt");
					if (fp != NULL)
					{
	sprintf(callbuf,"edquota -p null %s",entry->d_name);
			system(callbuf);
	sprintf(callbuf,"mkdir /import/homes/%s; chown %s.systemuser /import/homes/%s",entry->d_name,entry->d_name,entry->d_name);
			system(callbuf);
	sprintf(callbuf,"mkdir /import/homes/%s/web; chown %s.systemuser /import/homes/%s/web",entry->d_name,entry->d_name,entry->d_name);
			system(callbuf);
			
						fclose(fp);
						unlink(entry->d_name);
					}
// should go in the syslog...
//					fprintf(stderr,"Processed Activation for %s\n",entry->d_name);
	syslog(LOG_NOTICE,"Completed activation for user %s",entry->d_name);
                                
      				}
			}
			sleep(1);	// sleep another second...
	
		} while (entry != NULL);
		closedir(d);
	}
	return 0;
}

int daemonloop(void)
{

	do
	{
		sas_process_activations();

		sleep(60);	// sleep a minute ...
	} while (1);

	return 0;
}
 
int main(void)
{
	pid_t pid = 0;
	FILE *fp = NULL;

	if (getuid() != 0)
	{
		fprintf(stderr,"SAS/daemon needs to run as root!\n");
		return -1;
	}

	daemon(1,1);

	// pid query goes here, as daemon forks()
	pid = getpid();

	// write pid file
	fp = fopen ("/var/run/qbe-sas-daemon.pid","w");
	if (fp == NULL) {  fprintf(stderr,"qbed: cannot write PID file\n"); return 0; }
	fprintf(fp,"%d",pid);
	fclose(fp);

	openlog("qbe-sas-daemon", LOG_PID, LOG_DAEMON);
	
	syslog(LOG_INFO,"Qbe SAS Daemon (c) Copyright 2003 Christian Hofstaedtler");
	syslog(LOG_NOTICE,"daemon startup with pid %d\n",pid);
 
	daemonloop();

	closelog();
	
	return 0;

}

