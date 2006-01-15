# Qbe Makefile
# Copyright (C) 2001-2004 Christian Hofstaedtler. All Rights Reserved.
#

QBEPROG=AppViewer
QBEPEXT=EXE
QBEEXTRAALL=
QBEEXTRALINK=
QBEEXTRALINKDEP=
QBEWINSYS=windows
QBENETCOMPILER=csc
QBENETTARGET=winexe
WITHSSL=
WITHNETLIB=no

PROG_OBJ = $(OUTDIR)\Applications.resources \
	   $(OUTDIR)\IEViewer.resources \
	   AxInterop.SHDocVw.dll


PROG_FILES = ..\Common\AssemblyInfo.cs \
	..\Common\version.cs \
	AssemblyInfo.cs \
	Applications.cs /res:$(OUTDIR)\Applications.resources \
	IEViewer.cs /res:$(OUTDIR)\IEViewer.resources \
	/reference:AxInterop.SHDocVw.dll

QBEEXTRACLEAN=-del SHDocVw.dll AxInterop.SHDocVw.dll

!include "../Common/ilogin.mak"

AxInterop.SHDocVw.dll: $(SYSTEMROOT)\system32\shdocvw.dll
	aximp $(SYSTEMROOT)\system32\shdocvw.dll /out:AxInterop.SHDocVw.dll /keyfile:..\\Common\\sgKey.snk /nologo /verbose
	copy AxInterop.SHDocVw.dll $(OUTDIR)\\
	copy SHDocVw.dll $(OUTDIR)\\
	copy AxInterop.SHDocVw.dll $(OUTDIR)\\..\\
	copy SHDocVw.dll $(OUTDIR)\\..\\

# - eof -

