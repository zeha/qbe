@echo off
REM
REM $Id: make.bat 167 2004-06-18 09:03:52Z ch $
REM (C) Copyright 2003 Christian Hofstaedtler
REM

set QBEOUTDIR=Q:\BIN\RETAIL\I386_32_2000

if %1~==~ goto start
set QBEOUTDIR=%1

:start
cpp -Wall -P -E prefix.cnsh -o prefix.nsh

cl /nologo /GA /O2 certimport.c /link /SUBSYSTEM:WINDOWS /OUT:%QBEOUTDIR%\CertImport.exe crypt32.lib user32.lib

pause

REM pushd %QBEOUTDIR%
REM tar cvfz QbeClient-XPlat.tar.gz QbeService.EXE
REM popd

%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 Tool_ZapBdfLic.nsi
%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 SASUpgrade.nsi
%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 SASAppExp.nsi

%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 SASFatClient.nsi

%DEVELDRIVE%\programme\nsis20\makensis.exe /DQBEOUTDIR=%QBEOUTDIR% /NOCD /V2 SASMiniClient.nsi

if %NOPAUSE%~==~ pause

