@echo off
del test_ldap.exe
cl /nologo test_ldap.c qbeldap.c wldap32.lib
test_ldap.exe