;
; $Id: SASClient.nsi 122 2004-06-02 10:41:50Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;

Function DotNetFXInstall

	ClearErrors
	Push $R0
	ReadRegStr $R0 HKLM "SOFTWARE\Microsoft\.NETFramework\policy\v1.1" "4322"
	Pop $R0
	IfErrors install updatedone

	install:

		DetailPrint "* Lade .NET Installation herunter ..."
		SetOutPath "$INSTDIR\Setup"
		SetOverwrite on
		Delete "dotnetfx.exe"
		NSISdl::download /TIMEOUT=300000 "${DOTNETFX_URL}" "$INSTDIR\Setup\dotnetfx.exe"
		Pop $R0
		StrCmp $R0 "success" installupdate
		Goto noupdate
		installupdate:
			DetailPrint "  Installation..."
			ExecWait '$INSTDIR\Setup\dotnetfx.exe /Q' 
		# /C:"install.exe /Q"'
			Goto updatedone
		noupdate:
			DetailPrint "  Kein .NET Installer gefunden, fahre fort..."
			Goto updatedone
	  
	updatedone:
		Return

FunctionEnd

Function ClientUninstallOldVersions

;	IfSilent UninstallDone	; skip over uninstall
;	IfFileExists "$INSTDIR\Uninstall.exe" UninstallOldVersion
;
;	UninstallDone:
;		RMDir /r "$INSTDIR"
;		Return
;
;	UninstallOldVersion:
;		MessageBox MB_OK "Die alte Qbe SAS Client Version wird jetzt deinstalliert. Drücken Sie OK um damit zu beginnen."
;		DetailPrint "Deinstallation läuft..."
;		ExecWait '"$INSTDIR\Uninstall.exe"'
;		Sleep 2500
;		MessageBox MB_OK "Drücken Sie OK wenn die Deinstallation abgeschlossen ist."
;		Goto UninstallDone

	;;
	;; unregister ooold dlls and kill some files from <=2.0
	;;
	DetailPrint "* Entferne alte Dateien ..."


	DeleteRegValue HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Run" "QbeStart"
	DeleteRegKey HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient"
	DeleteRegKey HKLM "SOFTWARE\Qbe\SAS\Client"

	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /K'
	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /c /u /silent /gac "$INSTDIR\QbeSAS.dll"'

	IfFileExists "$INSTDIR\Setup\netQbe.inf" UninstallOldNetQbe
		goto UninstallOldNetQbeDone

	UninstallOldNetQbe:
		ExecWait 'rundll32.exe setupapi.dll,InstallHinfSection DefaultUninstall 128 $INSTDIR\Setup\netQbe.inf'
	UninstallOldNetQbeDone:

	RMDir /r "$PROGRAMFILES\iLogin"
	RMDir /r "$SYSDIR\Qbe\Setup"
	Delete "$SYSDIR\Qbe\*.exe"
	Delete "$SYSDIR\Qbe\Q*.dll"
	Delete "$SYSDIR\Qbe\iL*.dll"	; dont kill m*.dll (msvcrt,mfc...)
	Delete "$SYSDIR\Qbe\*.ico"
	Delete "$SYSDIR\Qbe\*.reg"
	Delete "$SYSDIR\Qbe\*.hta"
	Delete "$SYSDIR\Qbe\*.tmp"
	Delete "$SYSDIR\Qbe\*.new"

	UnRegDll "$SYSDIR\iLoginCOM.dll"

	DeleteRegKey HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\iLogin"
	DeleteRegKey HKLM "SOFTWARE\iLogin"

	RMDir /r "$PROGRAMFILES\iLogin"
	RMDir /r "$SMPROGRAMS\Qbe SAS"

	Delete "$SMSTARTUP\Qbe iLogin.lnk"
	Delete "$SMPROGRAMS\Qbe iLogin.lnk"

FunctionEnd

Function ClientWriteUninstaller

 SetOutPath "$INSTDIR" 

 DetailPrint "* Schreibe Deinstallationsinformationen ..."
	WriteUninstaller "$INSTDIR\Setup\Uninstall.exe"

	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "DisplayName" "${PRODUCT_NAME}"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "UninstallString" "$INSTDIR\Setup\Uninstall.exe"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "Version" "${PRODUCT_VERSION}"

	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "DisplayVersion" "${PRODUCT_VERSION}"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "InstallLocation" "$INSTDIR"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "Publisher" "Qbe Austria"

	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "ProductID" "SITE-HTL2700"

	WriteRegDword HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "NoModify" 1
	WriteRegDword HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "NoRepair" 1

;	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient" "URLInfoAbout" "http://go.qbe.ch/go/qbe/sas/client/urlinfoabout?version=${PRODUCT_VERSION}"

	WriteRegStr HKLM "SOFTWARE\Qbe\SAS\Client" "Version" "${PRODUCT_VERSION}"

FunctionEnd

Function ClientPreClean
	SetOutPath "$INSTDIR" 

	; clean out some reg settings
	File "clean.reg"
	ExecWait 'regedit.exe /S "$INSTDIR\clean.reg"'
	Delete "clean.reg"


	; install prereq's
 DetailPrint "* Installiere Abhängigkeiten ..."
	Call DotNetFxInstall
	Call SysFilesInstall
	; install cert
	SetOutPath "$INSTDIR"
	File "${QBEOUTDIR}\CertImport.exe"
	File "CertRootCA.der"
	ExecWait '"$INSTDIR\CertImport.exe" CertRootCA.der'
	Delete "CertRootCA.der"
	Delete "CertImport.exe"
	; done


	;;
	;; new-style PFC (needs .NET already on the machine)
	;; 
 DetailPrint "* Pre Flight Check ..."
	Call ClientPFC


FunctionEnd

Function ClientStop
	SetOutPath "$INSTDIR\Setup"
	File "${QBEOUTDIR}\QbePFC.exe"
	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /R'
FunctionEnd
Function ClientPFC
	SetOutPath "$INSTDIR\Setup"
	File "${QBEOUTDIR}\QbePFC.exe"
	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /k'
FunctionEnd

Function ClientInstallAll

	;;
	;; New Files!!
	;; Finally!
	;;
 DetailPrint "* Installiere neue Software ..."
	; copy q background
	SetOutPath "$WINDIR\Web\Wallpaper"
	File "..\Common\QbeBackground1600x1200.png"

	SetShellVarContext All

	SetOutPath "$INSTDIR"
	File "SASClient.reg"
	File "${QBEOUTDIR}\QbeSVC.EXE"
	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /I "$INSTDIR\QbeSVC.EXE -start"'

	File "${QBEOUTDIR}\QbeTray.EXE"
	CreateShortCut "$SMPROGRAMS\Qbe SAS Client.lnk" "$INSTDIR\QbeTray.exe"

	File "${QBEOUTDIR}\QbeSAS.DLL"
	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /c /silent /gac "$INSTDIR\QbeSAS.DLL"'

	IfFileExists "$INSTDIR\qbelogon.cmd" dont_update_logoncmds
		File "${QBEOUTDIR}\QbeLogon.cmd"
		File "${QBEOUTDIR}\QbeLogout.cmd"
	dont_update_logoncmds:

	File "${QBEOUTDIR}\HTA\startup.hta"
	File "${QBEOUTDIR}\HTA\SASClient.hta"
	File "${QBEOUTDIR}\HTA\Q.ico"
  
;	SetOutPath "$INSTDIR\Setup"
;	File "netQbe.inf"
;	ExecWait 'rundll32.exe setupapi.dll,InstallHinfSection DefaultInstall 128 $INSTDIR\Setup\netQbe.inf'

 DetailPrint "* Final Cleanup ..."
	SetOutPath "$INSTDIR\Setup"
	Delete "bd7.exe"
	Delete "syslogin.exe"
	Delete "dotnetfx.exe"
FunctionEnd

Function ClientDistApp
	; application xml path in $R0
	ExecWait '"$INSTDIR\qbesvc.exe" -distapp "$R0"'
FunctionEnd

Function un.ClientUninstall
	SetShellVarContext All
	Delete "$SMPROGRAMS\Qbe SAS Client.lnk"

	DeleteRegValue HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Run" "QbeStart"
	DeleteRegKey HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASClient"
	DeleteRegKey HKLM "SOFTWARE\Qbe\SAS\Client"
	DeleteRegKey HKLM "SOFTWARE\Qbe"

	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /K'
	ExecWait '"$INSTDIR\Setup\QbePFC.exe" /c /u /silent /gac "$INSTDIR\QbeSAS.dll"'

;	ExecWait 'rundll32.exe setupapi.dll,InstallHinfSection DefaultUninstall 128 $INSTDIR\Setup\netQbe.inf'

	RMDir /r "$INSTDIR\Setup"
	RMDir /r "$INSTDIR"
FunctionEnd

