@echo off

set QBEOUTDIR=E:\QbeClient\BIN\RETAIL\I386_32_XP51

%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 SASfoo.nsi

%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 SASMiniClient.nsi

if %NOPAUSE%~==~ pause

pause
