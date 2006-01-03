;
; $Id: sysfiles.nsi 39 2004-04-16 06:05:14Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
SetCompressor lzma

!ifndef QBEOUTDIR
!define QBEOUTDIR ..\BIN\RETAIL\I386_32_2000
!endif

 !include "MUI.nsh"

!include "prefix.nsh"

  OutFile "${QBEOUTDIR}\sysfiles.exe"
  InstallDir "$SYSDIR"
  Name "System Files"

  BrandingText "Qbe Installation"
  ShowInstDetails show

  SetDateSave on
  SetOverwrite try
  SetCompress auto
  SetDatablockOptimize on

; !define MUI_WELCOMEFINISHPAGE_BITMAP "nsis\special.bmp"

 !define MUI_ICON "nsis\install.ico"
 !define MUI_UNICON "nsis\uninstall.ico"

 !define MUI_PROGRESSBAR smooth

; !define MUI_WELCOMEPAGE_TITLE "Systemdateien Update"

; !insertmacro MUI_PAGE_WELCOME
 !insertmacro MUI_PAGE_INSTFILES
; !insertmacro MUI_PAGE_FINISH

 !insertmacro MUI_LANGUAGE "GERMAN"

;--------------------------------
;Installer Sections

Section "x" SecCopyUI
 SetAutoClose true
 SetOutPath "$SYSDIR"
 !cd dlls
 ; 7.0
 ; msvcrt
 File "msvcr70.dll"
 File "msvcp70.dll"
 ; mfc
 File "mfc70u.dll"
 File "mfc70enu.dll"

 ; 7.1
 ; msvcrt
 File "msvcr71.dll"
 File "msvcp71.dll"
 ; mfc
 File "mfc71u.dll"
 File "mfc71deu.dll"
 File "mfc71enu.dll"
 !cd ..
SectionEnd

