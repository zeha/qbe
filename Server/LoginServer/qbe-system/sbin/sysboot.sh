#!/bin/sh
# VeRsIoN=0.001 SAS System Boot Script
# CoPyRiGhT=(C) 2003 Christian Hofstaedtler

echo "*LEGACY*"

case "$1" in
start)
	echo -n "Qbe SAS System boot-up..."
	echo -n "MOUNT "
	/qbe/sbin/mount-shares
	echo -n "CLEANUP "
	perl /qbe/sbin/qbe_inetstatus.pl 2>/dev/null

	echo "."
;;
stop)
	echo -n "Qbe SAS System stop..."
	/qbe/sbin/qbe_inetstatus.pl
	
	echo "."
;;
restart|force-reload|reload)
	$0 stop
	$1 start
;;
*)
	echo "Usage $0 "i \
		" {start|stop|restart)" >&2
	exit 1
;;
esac

exit 0

	
