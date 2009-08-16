#!/bin/sh
RESTART=1
if [ -f /sas/status/run/sshd.pid ]; then
kill -SIGHUP `cat /sas/status/run/sshd.pid`; RESTART=$?; fi
if [ $RESTART ]; then /sas/sbin/sasssh -f /sas/status/conf/sshd_config; fi
