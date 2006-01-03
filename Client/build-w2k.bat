@echo off
REM 
REM Qbe SAS Client/MSSDK Targeting for Windows 2000
REM $Id: build-w2k.bat 4 2004-03-09 23:02:29Z ch $
REM (C) Copyright 2003 Christian Hofstaedtler
REM
if %APPVER%~==5.0~ goto start
call "%DEVELDRIVE%\Programme\Microsoft Visual Studio .NET 2003\Vc7\bin\vcvars32.bat"
call "%DEVELDRIVE%\programme\microsoft sdk\setenv.bat" /2000 /RETAIL
set PATH=%PATH%;%DEVELDRIVE%\programme\cygwin\bin
:start
buildx.bat %1 %2 %3 %4 %5 %6 %7 %8 %9
