@echo off
REM
REM iLogin/MSSDK Targeting for Windows XP
REM $Id: build-wxp.bat 4 2004-03-09 23:02:29Z ch $
REM (C) Copyright 2003 Christian Hofstaedtler
REM
if %APPVER%~==5.01~ goto start
call "%DEVELDRIVE%\programme\microsoft sdk\setenv.bat" /XP32 /RETAIL
set PATH=%PATH%;%DEVELDRIVE%\programme\cygwin\bin
:start
pause
buildx.bat %1 %2 %3 %4 %5 %6 %7 %8 %9
