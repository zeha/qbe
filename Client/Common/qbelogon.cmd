@echo off
title Qbe SAS Client Login
echo.
echo.
echo   Qbe SAS Client Login
echo   $Id: qbelogon.cmd 85 2004-05-04 13:17:16Z ch $
echo.
echo     Windows Netzwerk Anmeldung laeuft...
REM net.exe use \\qbe-auth /USER:%1 %2 /PERSISTENT:NO
net.exe use \\qbe-auth %2 /USER:%1
net.exe use \\10.0.0.3 "" /USER:schueler
echo.
echo     Fertig.
echo.
