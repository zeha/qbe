
all: qbe_proxyacl qbe_writeacl
debug: qbe_proxyaclD qbe_writeaclD

install: all /usr/local/qbe_proxyacl
#	-mkdir /usr/local/qbe_proxyacl
	install -C qbe_writeacl /usr/local/qbe_proxyacl/
	install -C qbe_proxyacl /usr/local/qbe_proxyacl/
	install -C qbe_writeacl.pl /usr/local/qbe_proxyacl/
	install -C qbe_makeacl /usr/local/qbe_proxyacl/
	install -C qbetest.pl /usr/local/qbe_proxyacl/


 /usr/local/qbe_proxyacl:
	mkdir /usr/local/qbe_proxyacl

clean:
	@-rm -- qbe_proxyacl qbe_writeacl *~ core

qbe_proxyaclD: qbe_proxyacl.c qbe_proxyacl.h
	gcc qbe_proxyacl.c -o qbe_proxyacl -Wall

qbe_writeaclD: qbe_writeacl.c qbe_proxyacl.h
	gcc qbe_writeacl.c -o qbe_writeacl -Wall

qbe_proxyacl: qbe_proxyacl.c qbe_proxyacl.h
	gcc qbe_proxyacl.c -o qbe_proxyacl -D NDEBUG -Wall
	@strip qbe_proxyacl

qbe_writeacl: qbe_writeacl.c qbe_proxyacl.h
	gcc qbe_writeacl.c -o qbe_writeacl -D NDEBUG -Wall
	@strip qbe_writeacl
