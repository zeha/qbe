#!/bin/sh
#
#
# kill edvo user objects from our database

rm -f /qbe/data/edvolist
ldapsearch -x "(&(ou=EDVO)(objectClass=posixAccount))" dn | grep "dn: " | awk '{ print $2; }' > /qbe/data/edvolist
ldapdelete -f /qbe/data/edvolist -x -D cn=Admin,o=System -w XXX

######################################################
# -eof-
