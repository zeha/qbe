#!/bin/sh
VERSION=`QbeService-Mono.EXE -V | grep -o -E "[0-9]*\.[0-9]*\.[0-9]*"`
rm -rf qbe-install
mkdir qbe-install
cp QbeService-Mono.EXE qbe-install/
cp install.sh qbe-install/
echo $VERSION > qbe-install/version
mkdir qbe-install/scripts
cp -v scripts-src/* qbe-install/scripts/
makeself --notemp --gzip qbe-install install-linux "Qbe SAS Client (Mono) $VERSION" ./install.sh
rm -rf qbe-install

