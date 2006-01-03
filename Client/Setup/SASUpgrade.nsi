;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;
	; import $QBEOUTDIR
	!include "SASClient-BasePrefix.nsi"

	OutFile "${QBEOUTDIR}\QbeSASUpgrade.exe"
	InstallDir "$SYSDIR\Qbe"

; dummy
Function SysFilesInstall
FunctionEnd

	!include "SASClient-QbeClient.nsi"

!macro UpgradeSASFile FILENAME
	Delete "$INSTDIR\${FILENAME}.NEW"
	Delete "$INSTDIR\${FILENAME}"
	File /oname="${FILENAME}.NEW" "${QBEOUTDIR}\${FILENAME}"
	Rename /REBOOTOK "$INSTDIR\${FILENAME}.NEW" "$INSTDIR\${FILENAME}"
!macroend


Section "All" SecCopyUI
;	SetOutPath "$INSTDIR"

;	Call BitDefenderSettings
;	Call InstallWUSettings

	SetOutPath "$INSTDIR"
;	File "SASClient.reg"

	!insertmacro UpgradeSASFile "QbeSvc.EXE"
	!insertmacro UpgradeSASFile "QbeTray.EXE"

SectionEnd

