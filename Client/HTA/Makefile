# Qbe iLogin Makefile: GinaHook
# Copyright (C) 2001-2003 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=HTA
QBEPEXT=HTA
QBEPOBJ=iLogin
QBENOLINK=yes
QBEEXTRAALL=
QBEEXTRALINK=
QBEEXTRALINKDEP=
WITHSSL=
WITHNETLIB=no

PROG_OBJ = $(OUTDIR)\SASClient.hta \
	   $(OUTDIR)\startup.hta \
	   $(OUTDIR)\Q.ico 
#

!include "../Common/ilogin.mak"

$(OUTDIR)\SASClient.hta: SASClient.hta
	cpp -Wall -P -E $? -o $@

$(OUTDIR)\startup.hta: startup.hta
	cpp -Wall -P -E $? -o $@

$(OUTDIR)\Q.ico: ../Common/Q.ico
	copy ..\Common\Q.ico $(OUTDIR)\Q.ico


