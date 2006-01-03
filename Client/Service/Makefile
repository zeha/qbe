# Qbe iLogin Makefile: QbeHTA
# Copyright (C) 2001-2003 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=QbeSvc
QBEPEXT=EXE
QBEEXTRAALL=
QBEEXTRALINK=
QBEEXTRALINKDEP=
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
	   ..\Common\ilogin-version.cs \
	   ..\QbeSAS\ServiceMessageBox.cs \
	   ..\QbeSAS\WinServiceAPI.cs \
	   ..\QbeSAS\CaptureScreen.cs \
	   ..\QbeSAS\WinTimeAPI.cs \
	   /r:QbeUtil.dll

!include "../Common/ilogin.mak"

# - eof -

