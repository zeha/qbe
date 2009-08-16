#!/usr/bin/perl -w
#
# Outputs sysstate.php
#
# syscheck.pl - SAS 1.0 - 30. Juni 2002, 19:37:15 - ch
#


$status_sasd = `ps -A | grep "qbe-sas-daemon"`;
$status_ldap = `ps -A | grep "ndsd"`;
$status_mysql = `ps -A | grep "mysqld"`;
$status_http = `ps -A | grep "apache"`;
$status_dhcp = `ps -A | grep "dhcpd"`;
$status_samba = `ps -A | grep "smbd"`;

$status = 0;	# assume ok

# system will operate without sasd, but regular maintenance and 
# activations are not done
if ($status_sasd eq "") { $status = 1; }	#fail
# if ldap fails, system state is really critical !!!
if ($status_ldap eq "") { $status = 2; }	#crit
#
if ($status_mysql eq "") { $status = 1; }	#fail
if ($status_http eq "") { $status = 2; }	#crit
if ($status_samba eq "") { $status = 2; }	#crit
if ($status_dhcp eq "") { $status = 1; }	#fail

$output = '<? function sysstate($mode="html"){if($mode=="html"){$r="<span class=\"sysstate_';

if ($status == 0)	{ $output .= 'pass\">ok';	}
if ($status == 1)	{ $output .= 'fail\">failure';	}
if ($status == 2)	{ $output .= 'crit\">critical'; }

$output .= '</span>";}else{$r="';

if ($status == 0)	{ $output .= 'pass'; }
if ($status == 1)	{ $output .= 'fail'; }
if ($status == 2)	{ $output .= 'crit'; }

$output .= '";}return $r;} ?>';

open(SYSSTATEPHP, ">/sas/web/sysstate.php");
print SYSSTATEPHP $output;
close(SYSSTATEPHP);
