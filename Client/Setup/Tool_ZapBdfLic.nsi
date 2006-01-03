;
; This is a *tool* for bdf7lic.xml.
; It just zaps out old BitDefender licensing info from win.ini.
;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;

!ifndef QBEOUTDIR
!define QBEOUTDIR ..\BIN\RETAIL\I386_32_2000
!endif

	Name "ZAP"

	SilentInstall silent

	SetDateSave off
	SetCompress off
	SetDatablockOptimize off

	OutFile "${QBEOUTDIR}\ZapBdfLic.exe"
	InstallDir "$WINDIR"

Section "All" SecCopyUI
	DeleteINISec "$WINDIR\win.ini" "Internal"
	DeleteINISec "$WINDIR\win.ini" "Internal"
	DeleteINISec "$WINDIR\win.ini" "Internal"
	DeleteINISec "$WINDIR\win.ini" "Internal"
SectionEnd

