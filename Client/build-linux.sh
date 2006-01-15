#!/bin/sh
echo Qbe SAS Client Linux MakeRunner
echo "\$Id$"
echo Building common
cd Common
make -f Makefile.mono clean && make -f Makefile.mono
cd ..
echo Building service
cd Service
make -f Makefile.mono
cd ..
echo Building SetupLinux
cd SetupLinux
make targz
cd ..

