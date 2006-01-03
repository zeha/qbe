;
; $Id: SASClient.nsi 122 2004-06-02 10:41:50Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;

Var BITDEFENDER_VALID
Var BITDEFENDER_SELECTED

Function BitDefenderPageFunc

	Push $R0
	ClearErrors
	; Check if I can read the reg key:
	ReadRegStr $R0 HKLM "SOFTWARE\Softwin\BitDefender Desktop 7" "QuarantineDir"
	IfErrors no_bdf

		; all fine ->
		; User has BitDefender installed already
		Pop $R0
		StrCpy $BITDEFENDER_VALID "0"

		Abort

	no_bdf:
		; no i couldnt read it ->
		; user has no BD7 installed.
		Pop $R0
		StrCpy $BITDEFENDER_VALID "1"

		!insertmacro MUI_HEADER_TEXT "BitDefender 7 Professional" "AntiVirus Software in der HTBLuVA Wiener Neustadt"
		!insertmacro MUI_INSTALLOPTIONS_DISPLAY "bitdefender.ini"

		Return

FunctionEnd

Function BitDefenderInstallFromPage
	!insertmacro MUI_INSTALLOPTIONS_READ $BITDEFENDER_SELECTED "bitdefender.ini" "Field 2" "State"

	StrCmp $BITDEFENDER_VALID "1" bdf_check
	DetailPrint "AV: Already installed."
	Return

bdf_check:
	StrCmp $BITDEFENDER_SELECTED "1" bdf_go
	DetailPrint "AV: User selected no-install."
	Return

bdf_go:
	Call BitDefenderInstallExec
FunctionEnd

Function BitDefenderInstallNoPage
	Push $R0
	ClearErrors
	; Check if I can read the reg key:
	ReadRegStr $R0 HKLM "SOFTWARE\Softwin\BitDefender Desktop 7" "QuarantineDir"
	IfErrors no_bdf

		; all fine ->
		; User has BitDefender installed already
		Pop $R0
		Return
		;Abort

	no_bdf:
		; no i couldnt read it ->
		; user has no BD7 installed.
		Pop $R0
		Call BitDefenderInstallExec

FunctionEnd

Function BitDefenderInstallExec
	Push $R0

	CopyFiles "$WINDIR\win.ini" "$WINDIR\win.qbe"

	; Licensing Informationen installieren
	StrCpy "$R0" "http://qbe-auth.htlwrn.ac.at/applications/bdf7lic.xml"
	Call ClientDistApp

	DetailPrint "* Lade BD7 Installation herunter ..."
	SetOutPath "$INSTDIR\Setup"
	SetOverwrite on
	Delete "$INSTDIR\Setup\BitDefender7Professional.exe"
	NSISdl::download /TIMEOUT=300000 "${BITDEFENDER_URL}" "$INSTDIR\Setup\BitDefender7Professional.exe"
	Pop $R0
	StrCmp $R0 "success" installupdate
	Goto noupdate
	installupdate:
		DetailPrint "  Installation..."
		ExecWait '$INSTDIR\Setup\BitDefender7Professional.exe'
		Call BitDefenderHTLSettings
		Delete '$INSTDIR\Setup\BitDefender7Professional.exe'

		;; wah damn hack... bd7 install sucks somehow...
		;; we have to restore win.ini and pretend we didn't just install a license code
		;; the msi somehow kills the serial number. weird.
		CopyFiles "$WINDIR\win.qbe" "$WINDIR\win.ini"
		Delete "$WINDIR\win.qbe"
		WriteRegStr HKLM "SOFTWARE\Qbe\Applications\BDF7Lic" "Version" "0"
		SetRebootFlag true
		Return
	noupdate:
		MessageBox MB_ICONEXCLAMATION "BitDefender 7 Download fehlgeschlagen!"
  		Return

FunctionEnd

!include "SASClient-BitDefenderSettings.nsi"


