@echo off
REM
REM The Qbe SAS Client MASTER build file
REM $Id: buildx.bat 152 2004-06-16 13:28:30Z ch $
REM (C) Copyright 2003, 2004 Christian Hofstaedtler
REM
REM Note: this was made between 23:00 and 00:32, so expect mistakes...
REM

set QBEWHAT=
set QBECLEAN=yes

if NOT %CYGWIN%~==~ goto cygok
set CYGWIN=%DEVELDRIVE%\Programme\Cygwin
set PATH=%PATH%;%CYGWIN%\bin

:cygok

if %APPVER%~==~ goto nosdk
set OLDNOPAUSE=%NOPAUSE%
title Qbe SAS Client System: Targeting Win32 %APPVER%

mkdir BIN
set QBEOUTDIR=..\BIN\DEBUG
if %NODEBUG%~==1~ set QBEOUTDIR=..\BIN\RETAIL
mkdir Common\%QBEOUTDIR%

set QBEWINVER=I386_32_%APPVER%
if %APPVER%~==4.0~ set QBEWINVER=I386_32_NT40
if %APPVER%~==5.0~ set QBEWINVER=I386_32_2000
if %APPVER%~==5.01~ set QBEWINVER=I386_32_XP51
if %APPVER%~==5.2~ set QBEWINVER=I386_32_2003

set QBEOUTDIR=%QBEOUTDIR%\%QBEWINVER%

if %1~==NOCL~ set QBECLEAN=NO
if %2~==NOCL~ set QBECLEAN=NO

REM set up nmake parameters
set QBENMAKE=nmake.exe /nologo QBEOUTDIR=%QBEOUTDIR%

if %1~==NOCL~ goto realstart

if NOT %1~==~ set QBEWHAT=SINGLE
if NOT %1~==~ goto start

cd Common
rd /s %QBEOUTDIR%
mkdir %QBEOUTDIR%
cd ..

goto start

:nosdk
echo SAS Client Build could not detect the Microsoft SDK
echo set up. Please either use Microsoft's SetEnv.bat
echo or the approaite rebuild-xxx.bat.
echo.
goto quit

:start
cls
REM iLogin makefiles/make.bat's will use this
REM to detect automatic builds
set NOPAUSE=1
REM
if %1~==~ echo *** Qbe SAS Client: Complete Build
if NOT %1~==~ echo *** Qbe SAS Client: Building "%1" only
echo *** Targeting: Win32 %APPVER%
echo *** OutputDir: %QBEOUTDIR%
echo.
cd
REM build version file everytime
:TARCommon
cd Common
%QBENMAKE%
cd ..
REM ok
if %1~==~ goto realstart
if %1~==Common~ goto done
goto TAR%1

:realstart

:TARGina
echo.
echo ::: QbeGina
cd QbeGina
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE%
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARNP
echo.
echo ::: QbeNP
cd QbeNP
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE%
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARHTA
echo.
echo ::: HTA
cd HTA
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARAppViewer
echo.
echo ::: AppViewer
cd AppViewer
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARSAS
echo.
echo ::: SAS
cd QbeSAS
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARLoginScript
echo.
echo ::: LoginScript
cd QbeLoginScript
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARTray
echo.
echo ::: Tray
cd QbeTray
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARService
echo.
echo ::: Service
cd Service
if %QBECLEAN%==yes %QBENMAKE% clean 
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARUnix
echo.
echo ::: Unix
cd Service
if %QBECLEAN%==yes %QBENMAKE% /f Makefile.unix clean 
%QBENMAKE% /f Makefile.unix
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:TARPFC
echo.
echo ::: QbePFC
cd QbePFC
if %QBECLEAN%==yes %QBENMAKE% clean
%QBENMAKE% 
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

pause
:TARSetup
echo.
echo ::: Setup
cd Setup
call make.bat %QBEOUTDIR%
cd ..
if ERRORLEVEL 1 echo Errors: %ERRORLEVEL% 
if ERRORLEVEL 1 pause
if %QBEWHAT%~==SINGLE~ goto done

:done
echo.
echo ::: Done!

:quit
set NOPAUSE=%OLDNOPAUSE%
set OLDNOPAUSE=

set QBEOUTDIR=
set QBEWINVER=

if %NOPAUSE%~==~ pause

