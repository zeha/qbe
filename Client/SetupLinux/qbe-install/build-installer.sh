#!/bin/sh
../QbeService.EXE -V | grep -o -E "[0-9]*\.[0-9]*\.[0-9]*" > version
rm -rf scripts
mkdir scripts
cp -v scripts-src/* scripts/
mv scripts-src ../
cd ..
VERSION=`./QbeService.EXE -V | grep "Service"`
makeself --notemp --gzip qbe-install install-linux "$VERSION for Linux" ./install.sh
cd qbe-install
mv ../scripts-src .
rm -rf scripts

