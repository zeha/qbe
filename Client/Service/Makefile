# Qbe Client Makefile: QbeService
# Copyright (C) 2001-2006 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=QbeSvc
QBEPEXT=EXE
QBEEXTRAALL=QbeUtil.dll
QBEEXTRALINK=
QBEEXTRALINKDEP=QbeUtil.dll
QBEWINSYS=console
QBENETCOMPILER=csc
QBENETTARGET=exe
WITHSSL=
WITHNETLIB=no

PROG_FILES = ..\Common\AssemblyInfo.cs \
	   Win32Main.cs \
	   ..\QbeSAS\SysState.cs \
	   ..\QbeSAS\AppDist.cs \
	   ..\QbeSAS\HttpService.cs \
	   ..\Common\version.cs \
	   ..\QbeSAS\ServiceMessageBox.cs \
	   ..\QbeSAS\WinServiceAPI.cs \
	   ..\QbeSAS\CaptureScreen.cs \
	   ..\QbeSAS\WinTimeAPI.cs \
	   /r:QbeUtil.dll

!include "../Common/ilogin.mak"

QbeUtil.dll: $(QBEOUTDIR)\QbeUtil\QbeUtil.dll
        copy $(QBEOUTDIR)\QbeUtil\QbeUtil.dll QbeUtil.dll

# - eof -

