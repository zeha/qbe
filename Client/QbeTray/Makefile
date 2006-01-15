# Qbe Client Makefile: QbeTray
# Copyright (C) 2001-2006 Christian Hofstaedtler.  All rights reserved.
#

## -> MFC
#QBECLIB=-D_MD -MD /D "_AFXDLL"
QBEPROG=QbeTray
QBEPEXT=EXE
# QBEPOBJ=QbeStart.exe
QBEEXTRAALL=
QBEEXTRALINK=
QBEEXTRALINKDEP=
QBEWINSYS=windows
QBENETCOMPILER=csc
QBENETTARGET=winexe
WITHSSL=
WITHNETLIB=no

PROG_OBJ = $(OUTDIR)\Splash.resources \
	   $(OUTDIR)\TrayNotify.resources \
	   $(OUTDIR)\Resource.resources

PROG_FILES = ..\Common\AssemblyInfo.cs \
	..\Common\Conform.cs \
	   Splash.cs \
	   /res:$(OUTDIR)\Splash.resources \
	   StartTray.cs \
	   Tray.cs \
	   TrayNotify.cs \
	   /res:$(OUTDIR)\TrayNotify.resources \
	   /res:$(OUTDIR)\Resource.resources \
	   ..\Common\version.cs \
	   ..\QbeSAS\ClientUI.cs ..\QbeSAS\SysState.cs \
	   /m:QbeSAS.StartTray

#	   /r:$(OUTDIR)\..\QbeSAS.dll \

!include "../Common/ilogin.mak"


# - eof -

