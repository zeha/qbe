#!/bin/bash

VERSION=`cat version`

echo "     ____  _               Qbe SAS Client (XPlat) $VERSION"
echo "    / __ \| |              for Linux Installations"
echo "   | |  | | |__   ___      "
echo "   | |  | | '_ \ / _ \     Copyright 2001-2006 Christian Hofstaedtler"
echo "   | |__| | |_) |  __/     "
echo "    \___\_\_.__/ \___|     The \"2006 Happy New Year\"-Edition"
echo ""
echo ""
read -p "Specify Destination Path: (/opt/qbe) " INSTALLPATH
if [ "$INSTALLPATH" == "" ]; then
	INSTALLPATH=/opt/qbe
fi

echo "Install location: $INSTALLPATH"
echo ""
read -p "Continue (y/n)? " answer
if [ "$answer" != "y" ]; then
	echo "Installation aborted."
	exit 1
fi

echo ""
rm -rf $INSTALLPATH
mkdir $INSTALLPATH
cp -a scripts/* ../QbeService.EXE $INSTALLPATH/

echo ""
echo ""
echo "Hints:"
echo ""
echo " * An rc.d script is provided in the file 'rc-script'. It was written for"
echo "   Debian Linux. You may have to update it for other OS versions."
echo ""
echo " * If you are using Gnome 2.x, you can use the authsave script to save "
echo "   your username and password. authdel will delete this for you. Then "
echo "   use qbelogon in your session config: it will start QbeService and log "
echo "   you on with your saved data."
echo ""

