# Qbe SAS Client Makefile: QbeNDI
# Copyright (C) 2001-2004 Christian Hofstaedtler.  All rights reserved.
#

## -> MFC
#QBECLIB=-D_MD -MD /D "_AFXDLL"
QBEPROG=QbePFC
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

PROG_FILES = ..\Common\AssemblyInfo.cs \
	..\Common\Conform.cs \
	   ProgramBase.cs FusionInstall.cs comreg.cs \
	   QbePfc.cs \
	   ..\QbeSAS\PFC.cs ..\QbeSAS\WinServiceAPI.cs ..\QbeSAS\WinVersionAPI.cs \
	   ..\Common\ilogin-version.cs \
	   /m:ComReg.Start

#	

#	   /r:$(OUTDIR)\..\QbeSAS.dll \


!include "../Common/ilogin.mak"

# - eof -

