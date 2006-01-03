;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;
	; import $QBEOUTDIR
	!include "SASClient-BasePrefix.nsi"
	!include "MUI.nsh"

	OutFile "${QBEOUTDIR}\QbeSASClient-foo.exe"

	; load basic stuff like designs, product names and whatnot
	!include "SASClient-Base.nsi"

	!insertmacro MUI_PAGE_WELCOME
	!insertmacro MUI_PAGE_INSTFILES
	!insertmacro MUI_PAGE_FINISH

;	!insertmacro MUI_UNPAGE_WELCOME
;	!insertmacro MUI_UNPAGE_CONFIRM
	!insertmacro MUI_UNPAGE_INSTFILES
;	!insertmacro MUI_UNPAGE_FINISH

	; this installer also supports: BitDefender

	!include "SASClient-BaseFuncs.nsi"

;--------------------------------
;Installer Sections

Section "All" SecCopyUI

	; the fat client writes this into the reg, so we can use it for upgrades.
	; we have to write it too...
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileUser" 			"1"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileUserAutostart" 	"1"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileUserWinup" 		"1"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileHTL" 				"0"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileHTLHDGuard"		"0"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileHTLSyslogin"		"0"

	Call BasePreInst

	Call ClientUninstallOldVersions

	Call ClientWriteUninstaller

	Call ClientInstallAll

	Call ClientPFC


SectionEnd

;--------------------------------
;Uninstaller Section

Section "Uninstall"
	SetShellVarContext all

	Call un.WindowsUpdateZap
	Call un.ClientUninstall
SectionEnd


