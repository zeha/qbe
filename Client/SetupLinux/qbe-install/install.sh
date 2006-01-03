#!/bin/bash

INSTALLPATH=/opt/qbe
VERSION=`cat version`

echo ""
echo "Qbe SAS Client $VERSION for Linux Installation"
echo "The \"2005==WE ARE STILL ALIVE PREVIEW\"-Edition"
echo "Copyright 2001-2005 Christian Hofstaedtler"
echo ""

rm -f .tempfile
whiptail --title "Installation Path" --backtitle "Qbe SAS Client $VERSION" --inputbox "\nQbe SAS Client $VERSION for Linux\nCopyright 2001-2005 Christian Hofstaedtler\n\nEnter the path for the Qbe SAS Client installation:" 14 70 "$INSTALLPATH" 2> .tempfile
INSTALLPATH=`cat .tempfile`
rm -f .tempfile

if [ "$?" == "1" ]; then
	echo "Installation aborted."
	exit 1
fi

if [ "$INSTALLPATH" == "" ]; then
	echo "Installation aborted."
	exit 1
fi

echo "Install location: $INSTALLPATH"
echo ""
echo -n "Continue (y/n)? "
read answer
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
echo "An rc.d script is in the file 'qbeservice'. It was written for"
echo "  Debian Linux. For others, you may have to update it."
echo "If you are using Gnome 2.x, you can use the authsave script to"
echo "  save your username and password. authdel will delete this for"
echo "  you. Then use qbelogon in your session config, it will start"
echo "  the QbeService and log you on with your saved data."
echo ""

