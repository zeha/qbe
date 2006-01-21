;
; $Id$
;

Var SYSFILE_NAME

Function SysFilesCheckAndDownload
 ClearErrors
 Push $R0
 Push $R1

	DetailPrint "   $SYSFILE_NAME"
	FindFirst $R0 $R1 "$SYSDIR\$SYSFILE_NAME"
	IfErrors sfi_recheck sfi_ok
	FindClose $R0

 sfi_recheck:
	FindFirst $R0 $R1 "$INSTDIR\$SYSFILE_NAME"
	IfErrors sfi_get sfi_ok
	FindClose $R0

 sfi_get:
	SetOverwrite on
	NSISdl::download /TIMEOUT=300000 "${SYSFILES_BASE_URL}$SYSFILE_NAME" "$INSTDIR\$SYSFILE_NAME"
	Pop $R0
	StrCmp $R0 "success" sfi_ok
		Goto noupdate
	noupdate:
		DetailPrint "   Datei nicht gefunden!"
		MessageBox MB_ICONSTOP "Konnte wichtige Systemdateien nicht herunterladen."
		Abort "Konnte wichtige Systemdateien nicht herunterladen."
		Goto sfi_ok
 sfi_ok:

 Pop $R1
 Pop $R0

FunctionEnd

Function SysFilesInstall
	; SetOutPath "$INSTDIR"
	DetailPrint "* Überprüfe Systemdateien ..."

;	StrCpy $SYSFILE_NAME "mfc70u.dll"
;	Call SysFilesCheckAndDownload

	StrCpy $SYSFILE_NAME "mfc71u.dll"
	Call SysFilesCheckAndDownload

;	StrCpy $SYSFILE_NAME "msvcr70.dll"
;	Call SysFilesCheckAndDownload

	StrCpy $SYSFILE_NAME "msvcr71.dll"
	Call SysFilesCheckAndDownload

;	StrCpy $SYSFILE_NAME "msvcp70.dll"
;	Call SysFilesCheckAndDownload

	StrCpy $SYSFILE_NAME "msvcp71.dll"
	Call SysFilesCheckAndDownload

FunctionEnd

; - eof -

