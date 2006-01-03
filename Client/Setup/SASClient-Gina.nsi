;
; $Id: SASLogin.nsi 74 2004-05-04 08:17:10Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;

;--------------------------------
;Installer Sections

Function InstallSASLogin
	Delete "$SYSDIR\QbeGina-Uninstall.exe"
	DeleteRegKey HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\QbeGina" 

	SetOutPath "$SYSDIR"

	!insertmacro UpgradeDLL "${QBEOUTDIR}\QbeGina.dll" "$SYSDIR\QbeGina.dll" "$SYSDIR"
	!insertmacro UpgradeDLL "${QBEOUTDIR}\QbeNP.dll" "$INSTDIR\QbeNP.dll" "$INSTDIR"

	; enable qbegina
	WriteRegDWORD HKLM "SYSTEM\CurrentControlSet\Services\QbeSvc" "Start" 2
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon" "GinaDLL" "QbeGina.dll"

	; local login = default
	ReadRegStr $0 HKLM "SYSTEM\CurrentControlSet\Control\ComputerName\ComputerName" "ComputerName"
	WriteRegStr HKLM "SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon" "DefaultDomainName" $0

	SetOutPath "$INSTDIR"
	; copy loginscript handler
	File ${QBEOUTDIR}\QbeLoginScript.exe

	Call SASGinaRegisterQbeNP

	Call SASGinaSanityCheck

	SetRebootFlag true
FunctionEnd

Function SASGinaSanityCheck
	; utility files
	SetOutPath "$TEMP"
	File "syslogin\groupadd.cmd"
	File "syslogin\searchhost.cmd"
	File "syslogin\ldapsearch.exe"
	File "syslogin\ldapsdk.dll"

	; add "Qbe SAS Users" as a local group 
	ExecWait "cmd.exe /c $TEMP\groupadd.cmd"
	IfSilent noSanityCheck
		; do some sanity checks
		ExecWait "cmd.exe /c $TEMP\searchhost.cmd $0"
	noSanityCheck:

	Delete "$TEMP\groupadd.cmd"
	Delete "$TEMP\searchhost.cmd"
	Delete "$TEMP\ldapsearch.exe"
	Delete "$TEMP\ldapsdk.dll"

	SetOutPath "$INSTDIR"
FunctionEnd

Function un.UninstallSASGina
 SetShellVarContext all

 ; remove our group
 SetOutPath "$TEMP"
 File "syslogin\groupdel.cmd"
 ExecWait "cmd.exe /c $TEMP\groupdel.cmd"

 DeleteRegKey HKLM "SYSTEM\CurrentControlSet\Services\QbeNP"
 WriteRegStr HKLM "SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon" "GinaDLL" "MSGINA.DLL"
 DeleteRegKey HKLM "SOFTWARE\Qbe\SAS\SystemLogin"

 SetOutPath "$SYSDIR"
 Rename "QbeGina.old" "QbeGina.ba2"
 Delete "QbeGina.ba2"
 Rename "QbeGina.dll" "QbeGina.bak"
 Delete /REBOOTOK "QbeGina.bak"

 SetOutPath "$INSTDIR"
 Rename "QbeNP.old" "QbeNP.ba2"
 Delete "QbeNP.ba2"
 Rename "QbeNP.dll" "QbeNP.bak"
 Delete /REBOOTOK "QbeNP.bak"

 Delete "$TEMP\groupdel.cmd"

; SetRebootFlag true
FunctionEnd

Function SASGinaRegisterQbeNP
 ; enable qbenp
 Push $R0
 Push $R1
 WriteRegStr HKLM "SYSTEM\CurrentControlSet\Services\QbeNP\NetworkProvider" "Name" "Qbe SAS Network Provider"
 WriteRegDWORD HKLM "SYSTEM\CurrentControlSet\Services\QbeNP\NetworkProvider" "Class" 2
 WriteRegExpandStr HKLM "SYSTEM\CurrentControlSet\Services\QbeNP\NetworkProvider" "ProviderPath" "%SystemRoot%\\system32\\Qbe\\QbeNP.DLL"
 ReadRegStr $R1 HKLM "SYSTEM\CurrentControlSet\Control\NetworkProvider\Order" "ProviderOrder"
 Push $R1
 Push "QbeNP"
 Call StrStr
 Pop $R0
 StrCmp $R0 "" installQbeNPReg qbenpdone
 
 installQbeNPReg:
; MessageBox MB_ICONINFORMATION "Installing QbeNP."
 WriteRegStr HKLM "SYSTEM\CurrentControlSet\Control\NetworkProvider\Order" "ProviderOrder" "$R1,QbeNP"
 qbenpdone:

 Pop $R1
 Pop $R0 
FunctionEnd

 ; StrStr
 ; input, top of stack = string to search for
 ;        top of stack-1 = string to search in
 ; output, top of stack (replaces with the portion of the string remaining)
 ; modifies no other variables.
 ;
 ; Usage:
 ;   Push "this is a long ass string"
 ;   Push "ass"
 ;   Call StrStr
 ;   Pop $R0
 ;  ($R0 at this point is "ass string")

 Function StrStr
   Exch $R1 ; st=haystack,old$R1, $R1=needle
   Exch    ; st=old$R1,haystack
   Exch $R2 ; st=old$R1,old$R2, $R2=haystack
   Push $R3
   Push $R4
   Push $R5
   StrLen $R3 $R1
   StrCpy $R4 0
   ; $R1=needle
   ; $R2=haystack
   ; $R3=len(needle)
   ; $R4=cnt
   ; $R5=tmp
   loop:
     StrCpy $R5 $R2 $R3 $R4
     StrCmp $R5 $R1 done
     StrCmp $R5 "" done
     IntOp $R4 $R4 + 1
     Goto loop
 done:
   StrCpy $R1 $R2 "" $R4
   Pop $R5
   Pop $R4
   Pop $R3
   Pop $R2
   Exch $R1
 FunctionEnd

