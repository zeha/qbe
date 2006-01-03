;
; $Id: winup.nsh,v 1.1 2004/03/04 07:08:37 ch Exp $
;

Function InstallWUSettings
  WriteRegStr HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate" "WUServer" "http://10.0.0.3"
  WriteRegStr HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate" "WUStatusServer" "http://10.0.0.3"

  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "AUOptions" "4"
  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "NoAutoUpdate" "0"
  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "NoAutoRebootWithLoggedOnUsers" "1"
  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "ScheduledInstallDay" "0"
  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "ScheduledInstallTime" "11"
  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "UseWUServer" "1"
  WriteRegDWORD HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate\AU" "RescheduleWaitTime" "5"

  WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\WindowsUpdate\Auto Update" "AUState" "2"
  WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\WindowsUpdate\Auto Update" "AUOptions" "4"
  DeleteRegValue HKLM "Software\Microsoft\Windows\CurrentVersion\WindowsUpdate\Auto Update" "LastWaitTimeout"

  ; set service (and dependencies) to auto-start
  WriteRegDWORD HKLM "SYSTEM\CurrentControlSet\Services\wuauserv" "Start" "2"
  WriteRegDWORD HKLM "SYSTEM\CurrentControlSet\Services\BITS" "Start" "2"
  WriteRegDWORD HKLM "SYSTEM\CurrentControlSet\Services\cryptsvc" "Start" "2"

  DetailPrint "Windows Update Settings installed!"
  Sleep 2000

FunctionEnd

Function WindowsUpdateZap
	; delete windowsupdate settings
	DeleteRegKey HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate"
FunctionEnd

Function un.WindowsUpdateZap
	; delete windowsupdate settings
	DeleteRegKey HKLM "Software\Policies\Microsoft\Windows\WindowsUpdate"
FunctionEnd
; eof

