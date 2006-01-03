#
# CoPyRiGhT=(C) 2004 Christian Hofstaedtler
# VeRsIoN=Makefile for ip_ldap 1.0
# 
CC=gcc
CFLAGS = -g -O2 -Wall -D_REENTRANT

proxy_ip_ldap: ip_ldap.o Makefile
	gcc  -g -O2 -Wall -D_REENTRANT  -g -o proxy_ip_ldap  ip_ldap.o -lldap -lpthread -lm -lresolv -lnsl

clean:
	rm -f dict.o ip_ldap.o proxy_ip_ldap

