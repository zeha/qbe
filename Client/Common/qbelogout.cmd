@echo off
title Qbe SAS Client Logout
echo.
echo.
echo   Qbe SAS Client Logout
echo   $Id: qbelogout.cmd 85 2004-05-04 13:17:16Z ch $
echo.
echo     Windows Netzwerk Abmeldung laeuft...
net.exe use \\qbe-auth /del
net.exe use \\htl-e /del
echo.
echo     Fertig.
echo.
