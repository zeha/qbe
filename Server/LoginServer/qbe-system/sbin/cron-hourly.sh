#!/bin/sh
#
# SAS 1.0 - (c) Copyright 2002 Christian Hofstaedtler
#
# cron-hourly.sh    - executed every hour by cron
#
######################################################

# check if dns is still running...
/sas/sbin/dns-runcheck.sh

# synchronise time ...
#rdate -s time.vbs.at

######################################################
# -eof-