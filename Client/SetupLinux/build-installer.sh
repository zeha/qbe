#!/bin/sh
# get the reported version
VERSION=`./QbeService-Mono.EXE -V | grep -o -E "[0-9]*\.[0-9]*\.[0-9]*"`
# prepare qbe-install directory
rm -rf qbe-install
mkdir qbe-install
cp QbeService-Mono.EXE qbe-install/
cp install.sh qbe-install/
echo $VERSION > qbe-install/version
# prepare qbe-install/scripts/
mkdir qbe-install/scripts
cp -v scripts/* qbe-install/scripts/
# make it
makeself --notemp --gzip qbe-install install-linux "Qbe SAS Client (Mono) $VERSION" ./install.sh
rm -rf qbe-install

