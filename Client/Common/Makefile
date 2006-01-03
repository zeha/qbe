# Makefile. Written for UNIX. May work on windows with nmake too.
#

all: ilogin-version.cs AssemblyInfo.cs $(QBEOUTDIR)\qbelogon.cmd $(QBEOUTDIR)\qbelogout.cmd

clean:
	rm -f ilogin-version.cs AssemblyInfo.cs

ilogin-version.cs: ilogin-version.xcs ilogin-version.h 
	cpp -Wall -P -E ilogin-version.xcs -o ilogin-version.cs
	
AssemblyInfo.cs: AssemblyInfo.xcs ilogin-version.h
	cpp -Wall -P -E AssemblyInfo.xcs -o AssemblyInfo.cs

$(QBEOUTDIR)\qbelogon.cmd: qbelogon.cmd
	copy qbelogon.cmd $(QBEOUTDIR)\qbelogon.cmd

$(QBEOUTDIR)\qbelogout.cmd: qbelogout.cmd
	copy qbelogout.cmd $(QBEOUTDIR)\qbelogout.cmd

