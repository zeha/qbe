# This is the nmake Makefile for Qbe\Client\Common
#


all: version.cs AssemblyInfo.cs $(QBEOUTDIR)\qbelogon.cmd $(QBEOUTDIR)\qbelogout.cmd

clean:
	rm -f build.h version.cs AssemblyInfo.cs

version.cs: version.xcs version.h build.h
	cpp -Wall -P -E version.xcs -o version.cs
	
AssemblyInfo.cs: AssemblyInfo.xcs version.h build.h
	cpp -Wall -P -E AssemblyInfo.xcs -o AssemblyInfo.cs

$(QBEOUTDIR)\qbelogon.cmd: qbelogon.cmd
	copy qbelogon.cmd $(QBEOUTDIR)\qbelogon.cmd

$(QBEOUTDIR)\qbelogout.cmd: qbelogout.cmd
	copy qbelogout.cmd $(QBEOUTDIR)\qbelogout.cmd

build.h:
	if not exist build.h copy build.h.win32 build.h

#	rm -f build.h
#	echo "#define ILOGIN_CVSID \"Id: `svn up>/dev/null 2>/dev/null && svn info . | grep "^Revision: " | egrep -o "([0-9]+)"` `id -un` `LC_ALL=C date -u +"%Y-%m-%d %T UTC"`\"" > build.h

