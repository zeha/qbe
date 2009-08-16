DESTDIR=~/test/
CFLAGS+=-s
QBEPROG=qbe-filexs
QBEDIR=$(DESTDIR)/qbe

all:	$(QBEPROG)

$(QBEPROG): main.o filexs_fileget.o filexs_fileput.o filexs_rename.o filexs_delete.o filexs_rmdir.o filexs_mkdir.o
#	ld -O -lc -o $(QBEPROG) $+
	gcc -o $(QBEPROG) $+


clean:
	@-rm $(QBEPROG)
	@-rm *~
	@-rm *.o

install:	$(QBEPROG)
		install -d $(QBEDIR)
		install -d $(QBEDIR)/sbin
		install -m6555 ./qbe-filexs $(QBEDIR)/sbin

	
