DESTDIR=
QBEDIR=$(DESTDIR)/qbe

all: mkntpwd-target

mkntpwd-target: mkntpwd/mkntpwd

mkntpwd/mkntpwd:
	cd mkntpwd && make mkntpwd && cd ..
	
install:	mkntpwd-target
		install ./mkntpwd/mkntpwd $(QBEDIR)/sbin/mkntpwd


