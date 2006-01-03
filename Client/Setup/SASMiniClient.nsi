;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;
	; import $QBEOUTDIR
	!include "SASClient-BasePrefix.nsi"
	!include "MUI.nsh"

	OutFile "${QBEOUTDIR}\QbeSASClient-Mini.exe"

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
	!include "SASClient-BitDefender.nsi"

	!include "SASClient-BaseFuncs.nsi"

;--------------------------------
;Installer Sections

Section "All" SecCopyUI
	Call BitDefenderStop

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
	Call ClientPreClean

	Call InstallWUSettings

	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Run" "QbeStart" "$WINDIR\system32\Qbe\QbeTray.exe -auto"

	Call ClientInstallAll

	Call BitDefenderInstallNoPage
	; terminate bitdefender
	Call BitDefenderStop
	Call ClientPFC

	Call BitDefenderSettings
	IfRebootFlag bdfinstdone
		Call BitDefenderStart
	bdfinstdone:

SectionEnd

;--------------------------------
;Uninstaller Section

Section "Uninstall"
	SetShellVarContext all

	Call un.WindowsUpdateZap
	Call un.ClientUninstall
SectionEnd


