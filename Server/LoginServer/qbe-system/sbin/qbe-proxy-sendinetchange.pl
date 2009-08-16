#!/usr/bin/perl -w
#  Qbe SAS Proxy
#  (C) Copyright 2003 Christian Hofstaedtler
#  $Id$
############################
use Carp;
use strict;
require LWP::UserAgent;

package Qbe::Proxy::ACL::update;
$|=1;  # turn off buffering

use Net::LDAP;
my $ldap = Net::LDAP->new('qbe-auth.htlwrn.ac.at') or die "$@";

# system("rm -f /qbe/data/squid.old");
# system("cp /qbe/data/squid.include /qbe/data/squid.old");

#my $SQUIDFILE;
#open($SQUIDFILE, ">/qbe/data/squid.include");
#print $SQUIDFILE "127.0.0.1/32\n"; # to keep squid quiet if no other entries exist

my $results = $ldap->search ( base => "o=htlwrn,c=at",
			filter => "(|(inetStatus=0) (inetStatus=7))"
		);

$results->code && die $results->error;

my @updateHosts;

my $entry;
my $username; my $logonHost; my $mac; my $entryok; my $gidnumber;
foreach $entry ($results->all_entries) { 
	$entryok = 1;
        $username = $entry->get_value("uid") || "";
        if ($username eq "") { $entryok = 0; }
        $logonHost = $entry->get_value("loggedonHost") || "";
        if ($logonHost eq "") { $entryok = 0; }
        $mac = $entry->get_value("loggedonMac") || "";
        if ($mac eq "") { $entryok = 0; }
	$gidnumber = $entry->get_value("gidNumber") || "";
	if ($gidnumber eq "5002")
	{ $entryok = 1; $logonHost = $entry->get_value("ipHostNumber") || ""; }
	if ($logonHost eq "") { $entryok = 0; }


	if ($entryok == 1)
	{
#		print $SQUIDFILE "$logonHost/32\n";
		push @updateHosts, $logonHost.":0";
	}
}

$ldap->unbind;

#close($SQUIDFILE);

#my $RESULT;
#$RESULT = system("cmp -s /qbe/data/squid.old /qbe/data/squid.include");
#if ($RESULT != 0)
#{
#	system("/etc/init.d/squid reload >/dev/null");
#}

my $updateI = 0;
my $updateLine = "";
while ($_ = shift(@updateHosts))
{
	$updateLine .= "&hosts[" . $updateI . "]=" . $_;
	$updateI++;
	if ($updateI > 5)
	{
		my $ua = LWP::UserAgent->new(env_proxy => 0,keep_alive => 0,timeout => 180);
		my $resp = $ua->get('http://10.0.2.100/rpc/client/proxy-statusupdate?' . $updateLine);
		#print $updateLine . "->" .$resp->content ."\n";

		$updateLine = "";
		$updateI = 0;
	}
}

if ($updateLine ne '')
{
	my $ua = LWP::UserAgent->new(env_proxy => 0,keep_alive => 0,timeout => 180);
	my $resp = $ua->get('http://10.0.2.100/rpc/client/proxy-statusupdate?' . $updateLine);
	#print $updateLine . "->" .$resp->content ."\n";
}	


##
## -EOF-
##
#
