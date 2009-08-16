#!/bin/sh
#
# SAS 0.5 - (c) Copyright 2002-2003 Christian Hofstaedtler
#
# cron-10min.sh    - executed every 10 minutes by cron
#
######################################################

## updates dhcp config

# make a backup for compare...
cp /qbe/status/hosts/autoconf.dhcp /qbe/status/hosts/autoconf.old
# generate new list
##perl /qbe/sbin/qbe_dhcpconf.pl >/qbe/status/hosts/autoconf.dhcp 2>/dev/null
perl /qbe/sbin/qbe-computer-dhcp.pl >/qbe/status/hosts/autoconf.dhcp 2>/dev/null
# restart dhcp if configfile has changed
cmp -s /qbe/status/hosts/autoconf.dhcp /qbe/status/hosts/autoconf.old
if [[ $? ]]; then
	if [ -x /etc/init.d/dhcp ]; then
		/etc/init.d/dhcp restart
	fi;
	if [ -x /etc/init.d/dhcp3-server ]; then
		/etc/init.d/dhcp3-server restart >/dev/null
	fi;
fi;

## clean expired session
perl /qbe/sbin/qbe_cleansessions.pl >/dev/null 2>/dev/null

######################################################
# -eof-
