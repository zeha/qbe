;
; $Id$
;

Function BitDefenderSettings
	; Licensing Informationen installieren
	SetOutPath "$INSTDIR"
	StrCpy "$R0" "http://qbe-auth.htlwrn.ac.at/applications/bdf7lic.xml"
	Call ClientDistApp

	Push $R0
	ClearErrors
	ReadRegStr $R0 HKLM "SOFTWARE\Softwin\BitDefender Desktop 7" "QuarantineDir"
	IfErrors updatedone install

	install:
		WriteINIStr "$R0\live.ini" "Settings" "Auto"		"1"
		WriteINIStr "$R0\live.ini" "Settings" "Location"	"http://bitdefender.htlwrn.ac.at"
		WriteINIStr "$R0\live.ini" "Settings" "UseProxy"	"0"
		WriteINIStr "$R0\live.ini" "Settings" "Ask"		"0"
		FlushINI "$R0\live.ini"
		DetailPrint "BitDefender Update Settings installed!"
		Sleep 2000
		Pop $R0
		Return

	updatedone:
		DetailPrint "BitDefender Update Settings NOT installed!"
		Pop $R0
		Return

FunctionEnd

Function BitDefenderHTLSettings

	Push $R0
	ClearErrors
	ReadRegStr $R0 HKLM "SOFTWARE\Softwin\BitDefender Desktop 7" "QuarantineDir"
	IfErrors updatedone install

	install:
		WriteINIStr "$R0\vshnet.ini" "Settings" "ScanEmail"		"0"
		WriteINIStr "$R0\vshnet.ini" "Settings" "ScanNet"		"0"
		WriteINIStr "$R0\vshnet.ini" "Settings" "ScanDial"		"1"
		WriteINIStr "$R0\vshnet.ini" "Settings" "ScanContent"	"0"
		WriteINIStr "$R0\vshnet.ini" "Settings" "ScanPrivacy"	"0"
		FlushINI "$R0\vshnet.ini"

		WriteINIStr "$R0\vshield.ini" "Settings" "Enabled"		"1"
		WriteINIStr "$R0\vshield.ini" "Settings" "FileMon"		"1"
		WriteINIStr "$R0\vshield.ini" "Settings" "RegMon"		"0"
		WriteINIStr "$R0\vshield.ini" "Settings" "NetMon"		"0"
		WriteINIStr "$R0\vshield.ini" "Settings" "ShowFZone"	"0"
		WriteINIStr "$R0\vshield.ini" "Settings" "ShowNZone"	"0"
		FlushINI "$R0\vshield.ini"

		DetailPrint "BitDefender Configuration installed!"
		Sleep 2000
		Pop $R0
		Return

	updatedone:
		DetailPrint "BitDefender Configuration NOT installed!"
		Pop $R0
		Return

FunctionEnd

Function BitDefenderStop

	ExecWait "net.exe stop vsserv"
	ExecWait "net.exe stop bdss"
	ExecWait "net.exe stop xcomm"
	
FunctionEnd

Function BitDefenderStart

	ExecWait "net.exe start xcomm"
	ExecWait "net.exe start bdss"
	ExecWait "net.exe start vsserv"
	
FunctionEnd

