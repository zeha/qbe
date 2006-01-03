@echo off
cls
echo.
echo.
echo   Skript: Host-Objekt pruefen
echo   $Id: searchhost.cmd 16 2004-03-11 13:28:42Z ch $
echo.
echo.
ldapsearch -x -h qbe-auth.htlwrn.ac.at cn=%1$ -A ipHostNumber qbePolicyName
echo.
pause
