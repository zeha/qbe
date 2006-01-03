# Qbe iLogin Makefile: QbeNP
# Copyright (C) 2001-2004 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=QbeNP
QBEPEXT=dll
QBEPOBJ=QbeNP.dll
QBEEXTRAALL=
QBEEXTRALINK=/dll /def:"QbeNP.def" User32.lib wldap32.lib netapi32.lib mpr.lib
QBEEXTRALINKDEP=
QBEWINSYS=console,$(APPVER)
WITHSSL=
WITHNETLIB=no

PROG_OBJ = $(OUTDIR)\QbeNP.obj \
	   $(OUTDIR)\QbeNP.res 

!include "../Common/ilogin.mak"

