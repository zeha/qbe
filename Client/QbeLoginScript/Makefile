# Qbe Makefile
# Copyright (C) 2001-2004 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=QbeLoginScript
QBEPEXT=EXE
QBEEXTRAALL=
QBEEXTRALINK=
QBEEXTRALINKDEP=
QBEWINSYS=windows
QBENETCOMPILER=csc
QBENETTARGET=winexe
WITHSSL=
WITHNETLIB=no

PROG_OBJ = $(OUTDIR)\LoginScript.resources \
	   $(OUTDIR)\InputDialog.resources

PROG_FILES = ..\Common\AssemblyInfo.cs \
	..\Common\Conform.cs \
	   LoginScript.cs \
	   /res:$(OUTDIR)\LoginScript.resources \
	   InputDialog.cs \
	   /res:$(OUTDIR)\InputDialog.resources \
	   ..\Common\ilogin-version.cs \
	   /m:QbeSAS.LoginScript


!include "../Common/ilogin.mak"


# - eof -

