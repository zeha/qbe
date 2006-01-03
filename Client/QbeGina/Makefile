# Qbe iLogin Makefile: GinaHook
# Copyright (C) 2001-2003 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=QbeGina
QBEPEXT=dll
#QBEPOBJ=QbeGina.dll
QBEEXTRAALL=
QBEEXTRARC=/d "UNICODE" /l 0x409 /d "NDEBUG"
QBEEXTRALINK=/dll /def:"QbeGina.def" mpr.lib wldap32.lib user32.lib Netapi32.lib winhttp.lib gdi32.lib
QBEEXTRALINKDEP=
QBEWINSYS=windows,$(APPVER)
WITHSSL=
WITHNETLIB=no

PROG_OBJ = $(OUTDIR)\QbeGina.obj \
	   $(OUTDIR)\qbeldap.obj \
	   $(OUTDIR)\qbereg.obj \
	   $(OUTDIR)\qbesvc.obj \
	   $(OUTDIR)\fileutil.obj \
	   $(OUTDIR)\QbeGina.res
	   #
	   #$(OUTDIR)\GinaDlg.obj \
	   #

!include "../Common/ilogin.mak"



## Client dependencies

#$(OUTDIR)\client.exe: $(OUTDIR)\client.obj
#    $(link) $(ldebug) $(conlflags) -out:$(OUTDIR)\client.exe $(OUTDIR)\client.obj $(conlibsmt)



