# Makefile. Written for UNIX. May work on windows with nmake too.
#

all: version.cs AssemblyInfo.cs $(QBEOUTDIR)\qbelogon.cmd $(QBEOUTDIR)\qbelogout.cmd

clean:
	rm -f version.cs AssemblyInfo.cs

version.cs: version.xcs version.h 
	cpp -Wall -P -E version.xcs -o version.cs
	
AssemblyInfo.cs: AssemblyInfo.xcs version.h
	cpp -Wall -P -E AssemblyInfo.xcs -o AssemblyInfo.cs

$(QBEOUTDIR)\qbelogon.cmd: qbelogon.cmd
	copy qbelogon.cmd $(QBEOUTDIR)\qbelogon.cmd

$(QBEOUTDIR)\qbelogout.cmd: qbelogout.cmd
	copy qbelogout.cmd $(QBEOUTDIR)\qbelogout.cmd

