dummy:
	@echo "Available targets: install-linux test build targz"

install-linux: QbeService.EXE
	cd qbe-install && ./build-installer.sh

clean:
	rm -rf test build install-linux QbeService.EXE targz QbeClient-XPlat.tar.gz

test:	install-linux QbeService.EXE
	rm -rf test
	mkdir test
	cp install-linux test/
	cp QbeService.EXE test/

build:	clean test
	rm -rf build
	mkdir build
	cp -a test build/ && mv build/test build/QbeService-XPlat

targz:	clean build
	cd build && tar cvfz ../QbeClient-XPlat.tar.gz QbeService-XPlat && touch targz

QbeService.EXE: ../Service/QbeService.EXE
	cp ../Service/QbeService.EXE .

