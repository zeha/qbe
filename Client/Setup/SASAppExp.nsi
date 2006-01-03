;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;
SetCompressor zlib

!ifndef QBEOUTDIR
!define QBEOUTDIR ..\BIN\RETAIL\I386_32_2000
!endif

	!include "prefix.nsh"
	Name "Qbe SAS Application Explorer"

	SetDateSave on
	SetCompress auto
	SetDatablockOptimize on

	OutFile "${QBEOUTDIR}\QbeSASAppExp.exe"
	InstallDir "$PROGRAMFILES\QbeSASTools"

Page directory
Page instfiles
UninstPage uninstConfirm
UninstPage instfiles

!macro UpgradeSASFile FILENAME
	Delete "$INSTDIR\${FILENAME}.NEW"
	Delete "$INSTDIR\${FILENAME}"
	File /oname="${FILENAME}.NEW" "${QBEOUTDIR}\${FILENAME}"
	Rename /REBOOTOK "$INSTDIR\${FILENAME}.NEW" "$INSTDIR\${FILENAME}"
!macroend

Section "All" SecCopyUI

	SetShellVarContext all
	SetOutPath "$INSTDIR"

	WriteUninstaller "$INSTDIR\AppExp-Uninstall.exe"

	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "DisplayName" "Qbe SAS Application Explorer"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "UninstallString" "$INSTDIR\AppExp-Uninstall.exe"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "Version" "${PRODUCT_VERSION}"

	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "DisplayVersion" "${PRODUCT_VERSION}"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "InstallLocation" "$INSTDIR"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "Publisher" "Christian Hofstaedtler"

	WriteRegDword HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "NoModify" 1
	WriteRegDword HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp" "NoRepair" 1

	!insertmacro UpgradeSASFile "AppViewer.exe"
	!insertmacro UpgradeSASFile "AxInterop.SHDocVw.dll"
	!insertmacro UpgradeSASFile "SHDocVw.dll"

	CreateShortCut "$SMPROGRAMS\Qbe Application Explorer.lnk" "$INSTDIR\AppViewer.exe"

SectionEnd

Section "Uninstall"
	SetShellVarContext all
	Delete "$INSTDIR\AppExp-Uninstall.exe"

	Delete "$SMPROGRAMS\Qbe Application Explorer.lnk"

	Delete "$INSTDIR\AppViewer.exe"
	Delete "$INSTDIR\AxInterop.SHDocVw.dll"
	Delete "$INSTDIR\SHDocVw.dll"

	DeleteRegKey HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeSASAppExp"

	RMDir "$INSTDIR"
SectionEnd
