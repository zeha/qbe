;
; $Id: patch.nsi,v 1.7 2004/03/04 07:08:37 ch Exp $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
SetCompressor lzma

!ifndef QBEOUTDIR
!define QBEOUTDIR ..\BIN\RETAIL\I386_32_2000
!endif

 !include "MUI.nsh"

!include "prefix.nsh"

  OutFile "${QBEOUTDIR}\patch.exe"
  InstallDir "$SYSDIR\Qbe\Setup"
  Name "Qbe Patch"

  BrandingText "Qbe Installation"
  ShowInstDetails show

  SetDateSave on
  SetOverwrite on
  SetCompress auto
  SetDatablockOptimize on

  VIAddVersionKey ProductName "Qbe SAS"
  VIAddVersionKey FileDescription "${PRODUCT_PRODUCT} Software Setup"
  VIAddVersionKey FileVersion "${PRODUCT_VERSION}"
  VIAddVersionKey ProductVersion "${PRODUCT_VERSION}"
  VIAddVersionKey CompanyName "Qbe Austria -- http://qbe.ch/"
  VIAddVersionKey InternalName "Qbe Software Setup"
  VIAddVersionKey OriginalFilename "QbeInstall.exe"
  VIAddVersionKey LegalCopyright "Copyright (C) 2001-2004 Christian Hofstaedtler"

  VIProductVersion "${PRODUCT_VERSION}.99"

 !include "${NSISDIR}\Include\WinMessages.NSH"

 !define MUI_WELCOMEFINISHPAGE_BITMAP "nsis\special.bmp"

 !define MUI_ICON "nsis\install.ico"
 !define MUI_UNICON "nsis\uninstall.ico"

; !define MUI_ABORTWARNING
 !define MUI_PROGRESSBAR smooth

 !define MUI_WELCOMEPAGE_TITLE "${PRODUCT_PRODUCT} Patch"


 !insertmacro MUI_PAGE_WELCOME
 !insertmacro MUI_PAGE_INSTFILES
 !insertmacro MUI_PAGE_FINISH

 !insertmacro MUI_LANGUAGE "GERMAN"

!include "sysfilecheck.nsh"
!include "winup.nsh"

;--------------------------------
;Installer Sections

Section "Client" SecCopyUI
 SetAutoClose false

 Call SysFilesInstall
 Call InstallWUSettings

 DetailPrint "* Bereite System vor ..."


	SetOutPath "$INSTDIR"
	File "Version.inf"
	File "${QBEOUTDIR}\QbeSAS.dll"

;	File "${QBEOUTDIR}\QbeSvc.exe"
;	File "${QBEOUTDIR}\QbeTray.exe"
;	File "${QBEOUTDIR}\QbeGina.dll"
;	File "${QBEOUTDIR}\QbeEvent.dll"
;	File "${QBEOUTDIR}\iLoginCOM.dll"
;
;	File "${QBEOUTDIR}\HTA\startup.hta"
;	File "${QBEOUTDIR}\HTA\iLogin.hta"

 DetailPrint "* Installiere neue Software ..."
	SetOutPath "$SYSDIR\Qbe"

	File "Version.inf"
	File "${QBEOUTDIR}\QbeSAS.dll"

	File "${QBEOUTDIR}\QbeSvc.exe"
	File "${QBEOUTDIR}\QbeTray.exe"
;	File "${QBEOUTDIR}\QbeGina.dll"
;	File "${QBEOUTDIR}\QbeEvent.dll"
;	File "${QBEOUTDIR}\iLoginCOM.dll"

	File "${QBEOUTDIR}\HTA\startup.hta"
	File "${QBEOUTDIR}\HTA\iLogin.hta"
  
SectionEnd

