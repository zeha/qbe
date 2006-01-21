#
# Qbe SAS Client 2.24+ Light-Weight Makefile Include
# 
# $Id: ilogin.mak 12 2006-01-15 06:07:37Z ch $
# (C) Copyright 2003-2006 Christian Hofstaedtler
#

# build everything by default
all: alltarget

##
## Defines
##

!IF "$(QBEOUTDIR)" == ""
QBEOUTDIR = THISBIN
!ENDIF

!IFNDEF QBEOUTDIRPREFIX
QBEOUTDIRPREFIX=
!ENDIF

QBEOUTDIRX = $(QBEOUTDIRPREFIX)$(QBEOUTDIR)

!IF "$(QBEOUTDIR)" != ""
OUTDIR = $(QBEOUTDIRX)\$(QBEPROG)
!ENDIF

!IF "$(QBEPOBJ)" == ""
QBEPOBJ = $(QBEPROG).$(QBEPEXT)
!ENDIF

##
## Targets
##

.SUFFIXES:

.SUFFIXES: .exe .obj .asm .c .cpp .cxx .bas .cbl .f .f90 .for .pas .res .rc .netmodule .cs .resx .resources

.cpp{$(OUTDIR)}.obj:
    $(cc) /c $(cdebug) $(cflags) $(QBECLIB) /Fo"$(OUTDIR)\\" /Fd"$(OUTDIR)\\" /I../Common $**

.c{$(OUTDIR)}.obj:
    @$(cc) /c $(cdebug) $(cflags) $(QBECLIB) /Fo"$(OUTDIR)\\" /Fd"$(OUTDIR)\\" /I../Common $**
 
.rc{$(OUTDIR)}.res:
    @echo RC: Out: $**
    @$(rc) $(QBERCFLAGS) /Fo"$*.res" $**

.cs{$(OUTDIR)}.netmodule:
    csc /out:"$*.netmodule" /nologo /o+ /target:module $**

.resx{$(OUTDIR)}.resources:
    resgen $** "$*.resources"

!IF "$(QBENOLINK)" == "yes"
QBEALLTARGET=$(PROG_OBJ)
!ELSE
QBEALLTARGET=$(OUTDIR)\$(QBEPOBJ)
!ENDIF

alltarget: $(OUTDIR) $(QBEEXTRAALL) $(QBEALLTARGET)

$(OUTDIR) :
	@if not exist $(OUTDIR) echo DIR : $(OUTDIR)
	@if not exist $(OUTDIR) mkdir $(OUTDIR)

clean:
	$(QBEEXTRACLEAN)
	$(CLEANUP)

#$(OUTDIR)\$(QBEPOBJ): $(PROG_OBJ) $(QBEEXTRALINKDEP) Makefile
#    @$(link) /nologo /MAP $(ldebug) \
#      /SUBSYSTEM:$(QBEWINSYS) /MACHINE:IX86 \
#      $(PROG_OBJ) $(PROG_LIBS) $(QBEEXTRALINK) \
#      /PDB:$(OUTDIR)\$(QBEPROG).PDB \
#      /PDBSTRIPPED:$(OUTDIR)\$(QBEPROG)S.PDB \
#      -out:$(OUTDIR)\$(QBEPOBJ) 
#


##
## -eof
##
