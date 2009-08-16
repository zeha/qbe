#!/bin/sh
#
# SAS 1.0 - (c) Copyright 2002 Christian Hofstaedtler
#
# cron-daily.sh    - executed every day by cron
#
######################################################

# remove old temp files ...
rm -rf /export/share-free/*

#
/qbe/sbin/qbe-custom-deledvousers.sh

# sis cleanup
/qbe/sbin/qbe-sis-deloldentries.php

######################################################
# -eof-
