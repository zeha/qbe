;
; $Id: SASFatClient.nsi 168 2004-06-18 09:38:34Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;
	; import $QBEOUTDIR
	!include "SASClient-BasePrefix.nsi"
	!include "MUI.nsh"

	OutFile "${QBEOUTDIR}\QbeSASClient-Fat.exe"

	; load basic stuff like designs, product names and whatnot
	!include "SASClient-Base.nsi"

	!insertmacro MUI_PAGE_WELCOME
	Page custom ProfilePageFunc
	Page custom BitDefenderPageFunc 
	!insertmacro MUI_PAGE_INSTFILES
	!insertmacro MUI_PAGE_FINISH

	!insertmacro MUI_UNPAGE_WELCOME
	!insertmacro MUI_UNPAGE_CONFIRM
	!insertmacro MUI_UNPAGE_INSTFILES
	!insertmacro MUI_UNPAGE_FINISH

	; this installer also supports: SASGina, BitDefender
	!include "SASClient-Gina.nsi"
	!include "SASClient-BitDefender.nsi"

	!include "SASClient-BaseFuncs.nsi"

;--------------------------------
; Install Functions
	Var PROFILE_USER
	Var PROFILE_USER_AUTOSTART
	Var PROFILE_USER_WINUP
	Var PROFILE_HTL
	Var PROFILE_HTL_HDGUARD
	Var PROFILE_HTL_SYSLOGIN

Function .onInit
	!insertmacro MUI_INSTALLOPTIONS_EXTRACT "profile.ini"
	!insertmacro MUI_INSTALLOPTIONS_EXTRACT "bitdefender.ini"
FunctionEnd

Function ProfilePageFunc
	!insertmacro MUI_HEADER_TEXT "Profilauswahl" "Vordefinierte Profile machen die Wahl zur Qual..."
	!insertmacro MUI_INSTALLOPTIONS_DISPLAY "profile.ini"
FunctionEnd

Function ClientReadProfileSel
	; what was selected?
	!insertmacro MUI_INSTALLOPTIONS_READ $PROFILE_USER               "profile.ini" "Field 2" "State"
	!insertmacro MUI_INSTALLOPTIONS_READ $PROFILE_USER_AUTOSTART     "profile.ini" "Field 3" "State"
	!insertmacro MUI_INSTALLOPTIONS_READ $PROFILE_USER_WINUP         "profile.ini" "Field 7" "State"
	!insertmacro MUI_INSTALLOPTIONS_READ $PROFILE_HTL                "profile.ini" "Field 4" "State"
	!insertmacro MUI_INSTALLOPTIONS_READ $PROFILE_HTL_HDGUARD        "profile.ini" "Field 5" "State"
	!insertmacro MUI_INSTALLOPTIONS_READ $PROFILE_HTL_SYSLOGIN       "profile.ini" "Field 6" "State"

	; save the values for upgrades
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileUser" 			"$PROFILE_USER"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileUserAutostart" 	"$PROFILE_USER_AUTOSTART"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileUserWinup" 		"$PROFILE_USER_WINUP"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileHTL" 				"$PROFILE_HTL"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileHTLHDGuard"		"$PROFILE_HTL_HDGUARD"
	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Setup" "ProfileHTLSyslogin"		"$PROFILE_HTL_SYSLOGIN"
FunctionEnd

Function ClientInstallProfile
	;;
	;; Profile config...
	;;
 DetailPrint "* Installiere Einstellungen ..."
	; wipe old autostart setting
	DeleteRegValue HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Run" "QbeStart"
	; wipe out old WU settings
	Call WindowsUpdateZap

	Call ClientReadProfileSel


	; jump over there
	StrCmp $PROFILE_USER "1" set_user
	StrCmp $PROFILE_HTL  "1" set_htl

	; settings for: user notebook
set_user:
	 StrCmp $PROFILE_USER_WINUP "0" set_user2
	 Call InstallWUSettings

set_user2:
	 ; check for autostart yes/no
	 StrCmp $PROFILE_USER_AUTOSTART "0" set_done
	 WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Run" "QbeStart" "$WINDIR\system32\Qbe\QbeTray.exe -auto"
	 Goto set_done


	; settings for: htl workstation
set_htl:
	 WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Run" "QbeStart" "$WINDIR\system32\Qbe\QbeTray.exe -auto"
;	 Call BitDefenderCheck
	 Call BitDefenderHTLSettings

         ; if !with_hdguard, jump nohdg
	 StrCmp $PROFILE_HTL_HDGUARD "0" set_htl_nohdg
set_htl2:
	; if !with_syslogin, jump done
	 StrCmp $PROFILE_HTL_SYSLOGIN "0" set_done
	 Call InstallSASLogin
	 Goto set_done
	 
set_htl_nohdg:
	; settings for: htl workstation without hdguard
		Call InstallWUSettings
	; jump back to 2nd htl check
		Goto set_htl2

set_done:
	SetOutPath "$INSTDIR"

FunctionEnd


;--------------------------------
;Installer Sections

Section "All" SecCopyUI
	Call BitDefenderStop

	Call BasePreInst
	Call ClientUninstallOldVersions

	Call ClientWriteUninstaller
	Call ClientPreClean

	Call ClientInstallProfile

	Call ClientInstallAll

	Call BitDefenderInstallFromPage
	; terminate bitdefender
	Call ClientPFC

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
	Call un.UninstallSASGina
	Call un.ClientUninstall
SectionEnd


