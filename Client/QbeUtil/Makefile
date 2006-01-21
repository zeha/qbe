# Qbe SAS Client Makefile: QbeUtil
# Copyright (C) 2001-2006 Christian Hofstaedtler.  All rights reserved.
#

QBEPROG=QbeUtil
QBEPEXT=dll
QBECLIB=/nologo /O2 /D "WIN32" /D "NDEBUG" /D "_WINDLL" /D "_MBCS" /FD /EHsc /MT /GS /W3 /c /Zi /clr /TP /Zl \
        /FU "C:\WINDOWS\Microsoft.NET\Framework\v1.1.4322\mscorlib.dll" /FU "C:\WINDOWS\Microsoft.NET\Framework\v1.1.4322\System.dll" 

PROG_OBJ = $(OUTDIR)\QbeUtil.obj \
	   $(OUTDIR)\QSam.obj \
	   $(OUTDIR)\StdAfx.obj \
	   $(OUTDIR)\AssemblyInfo.obj \
           $(OUTDIR)\NTDesktopUtil.obj



!include <Win32.mak>
cflags=
cdebug=
!include "../Common/qbeclient.mak"

$(OUTDIR)\$(QBEPOBJ): $(PROG_OBJ) Makefile
        link /OUT:$(OUTDIR)\$(QBEPOBJ) \
        /INCREMENTAL:NO /NOLOGO /DLL $(ldebug) /PDB:$(OUTDIR)\$(QBEPROG).PDB /FIXED:No \
        /noentry nochkclr.obj mscoree.lib \
        mpr.lib wldap32.lib user32.lib Netapi32.lib kernel32.lib user32.lib gdi32.lib \
        winspool.lib comdlg32.lib advapi32.lib shell32.lib ole32.lib oleaut32.lib uuid.lib \
        odbc32.lib odbccp32.lib \
        $(PROG_OBJ)

.cpp{$(OUTDIR)}.obj:
    $(cc) /c $(cdebug) /AI "$(OUTDIR)\\" $(cflags) $(QBECLIB) /Fo"$(OUTDIR)\\" /Fd"$(OUTDIR)\\" /I../Common $**

