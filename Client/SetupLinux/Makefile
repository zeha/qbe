dummy:
	@echo "Available targets: install-linux test build targz"

install-linux: QbeService-Mono.EXE
	./build-installer.sh

clean:
	rm -rf test build install-linux QbeService*.EXE targz QbeClient-XPlat.tar.gz qbe-install

test:	install-linux QbeService-Mono.EXE QbeService-XPlat.EXE
	rm -rf test
	mkdir test
	cp install-linux test/
	cp QbeService-Mono.EXE test/
	cp QbeService-XPlat.EXE test/

build:	clean test
	rm -rf build
	mkdir build
	cp -a test build/ && mv build/test build/QbeService-XPlat

targz:	clean build
	cd build && tar cvfz ../QbeClient-XPlat.tar.gz QbeService-XPlat && touch targz

QbeService-Mono.EXE: ../Service/QbeService-Mono.EXE
	cp ../Service/QbeService-Mono.EXE .

QbeService-XPlat.EXE: ../Service/QbeService-XPlat.EXE
	cp ../Service/QbeService-XPlat.EXE .

