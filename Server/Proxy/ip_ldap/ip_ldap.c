/*
 * Copyright (C) 2004 Christian Hofstaedtler
 *
 */

#define ROOT_DN "o=htlwrn,c=at"

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
// #include <syslog.h>
#include <ldap.h>
#include <arpa/inet.h>

#define BUFSIZE 1024
static int ldap_lookup (char *address);
static LDAP* ds;

int
main (int argc, char *argv[])
{
  char *cp;
  char *address;
  char line[BUFSIZE];
//  char logx[BUFSIZE];

  setbuf(stdout, NULL);
  
//  openlog("squidacl",LOG_PID,LOG_DAEMON);
//  syslog(LOG_WARNING,"Started.");

  ds = ldap_open("10.0.2.100",LDAP_PORT);
  
  while (fgets (line, sizeof (line), stdin)) {
//	  syslog(LOG_WARNING,line);
    if ((cp = strchr (line, '\n')) != NULL) {
      *cp = '\0';
    }
    if ((cp = strtok (line, " \t")) != NULL) {
      address = cp;
      // username = strtok (NULL, " \t");
    } else {
      fprintf (stderr, "helper: unable to read tokens\n");
      printf ("ERR\n");
      continue;
    }
    if ((ldap_lookup (address)) != 0) {
//	    sprintf(logx,"address: %s, result: OK",address);
//	    syslog(LOG_WARNING,logx);
      printf ("OK\n");
    } else {
//	    sprintf(logx,"address: %s, result: ERR",address);
//	    syslog(LOG_WARNING,logx);
      printf ("ERR error=\"403\"\n");
    }
  }
//  closelog();

  return 0;
}

static int ldap_lookup (char *address)
{
  char szSearch[BUFSIZE];
  char *getAttrs[] = { "uid", "inetStatus", NULL };
  LDAPMessage *res, *e;
  int rc;
  int master_rc;

  /* (&(inetStatus=0) */
  sprintf(szSearch,
		"(& (| (inetStatus=0) (inetStatus=7)) (| "
				"(&(ipHostNumber=%s) (objectClass=qbeOwnedObject)) "
				"(&(loggedonHost=%s) (loggedonMac=*)) "
		"))",
		  address, address);
  rc = 0;
  master_rc = 0;
  
  /* nothing found so far! */
  rc = ldap_search_s(ds, ROOT_DN, LDAP_SCOPE_SUBTREE, szSearch, getAttrs, 0, &res);
  if (rc)
	  return 0;

  if (ldap_count_entries(ds, res) == 0)
	  return 0;
 
  e = ldap_first_entry(ds,res);
  if (e != NULL)
  {
	char *a;
	BerElement *ber;
	char **vals;
	
	a = ldap_first_attribute( ds, e, &ber );
	while (strcasecmp(a,"inetstatus") != 0)
		a = ldap_next_attribute(ds,e,ber);

	if ( ( vals = ldap_get_values(ds, e, a) ) != NULL )
	{
		if (vals[0] != NULL)
		{
			int inetstatus = atoi(vals[0]);
			if ( (inetstatus == 0) || (inetstatus == 7) )
			{
				master_rc = 1;
			}
		}
		ldap_value_free(vals);
	}	 
  }
  ldap_msgfree(res);
  
  /* If no match was found we return 0 */
  return master_rc;
}

