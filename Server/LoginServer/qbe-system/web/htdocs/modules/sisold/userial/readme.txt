                         1-Wire Public Domain Kit

                         USerial Build for Linux 
                           Version 3.00 Beta 2

Introduction
------------

   This port was targeted for and tested on a RedHat Linux X86 machine.
   The provided makefile will build all of the example programs distributed 
   in the 3.00 Beta 1-Wire Public Domain release.

   Must specify target link file on the command line, i.e.:

       make all LINKFILE=win32lnk.o
       make atodtst LINKFILE=linuxlnk.o
       make temp LINKFILE=myWeirdOSlnk.c

   or, call make with either of the following two targets
   specified, 'win32' or 'linux':

       make win32
       make linux

   This will cause the make file to use the default link
   file for the target platform and build all applications.

   For documentation on the examples and the 1-Wire Public Domain
   kit in general please see the 'main' kit:
       http://www.ibutton.com/software/1wire/wirekit.html

Examples 
--------
   atodtst
   counter
   coupler
   debit
   debitvm
   fish
   gethumd
   initcopr
   initcoprvm
   initrov
   initrovvm
   jibload
   jibmodpow
   jibtest
   memutil
   mweather
   ps_check
   ps_init
   sha_chck
   sha_init
   swtloop
   swtoper
   swtsngl
   temp
   thermodl
   thermoms
   tm_check
   tm_init
   tstfind

Tool Versions
-------------

   $ make -version
   GNU Make version 3.77, by Richard Stallman and Roland McGrath.
   Copyright (C) 1988, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98
           Free Software Foundation, Inc.
   This is free software; see the source for copying conditions.
   There is NO warranty; not even for MERCHANTABILITY or FITNESS FOR A
   PARTICULAR PURPOSE.

   $ gcc -dumpversion
   egcs-2.91.66              

   $ gcc -dumpmachine
   i386-redhat-linux            
    
General Notes
-------------

   - Relevant 1-Wire Information:
     http://www.ibutton.com
     http://www.maxim-ic.com/

   - 1-Wire Mailing List:
     http://lists.dalsemi.com/mailman/listinfo/1-wire-software-development
