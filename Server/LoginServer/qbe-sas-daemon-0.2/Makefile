DESTDIR =
SBIN = $(DESTDIR)/qbe/sbin

CFLAGS=-O2 -Wall -s

all: qbe-sas-daemon

qbe-sas-daemon: qbe-sas-daemon.c

clean:
	@-rm qbe-sas-daemon 
	@-rm *~
	@-rm *.o

install:	qbe-sas-daemon
		install -d $(SBIN)
		install ./qbe-sas-daemon $(SBIN)

