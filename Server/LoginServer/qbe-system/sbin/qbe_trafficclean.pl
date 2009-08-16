#!/usr/bin/perl -w
#  Qbe trafficcheck 0.01
#  (C) Copyright 2002-2003 Christian Hofstaedtler
#  $Id: qbe_dhcpconf.pl,v 1.4 2002/11/11 13:15:00 ch Exp $
############################

use Carp;
use diagnostics;
use English;
use Net::LDAP;
use strict;
use Mysql;

print STDERR "Qbe trafficclean 0.01 / cleans traffic\n";
print STDERR "(C) Copyright 2002-2003 Christian Hofstaedtler\n";

package Qbe::Session::TrafficCheck;
$|=1;  # turn off buffering

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "trafficcheck: $@";

$ldap->bind('cn=Root,o=htlwrn,c=at',	
		password => 'XXX'
	) or die "trafficcheck: $@";

my $results = $ldap->search (
		base => "o=htlwrn,c=at",
		filter => "(&(uid=*) (!(traffic=0)) )",
	) or die "$@";

$results->code && die "Error: " . $results->error;

my $entry; my $traffic; my $inet; my $r;
foreach $entry ($results->all_entries) { 
        $traffic = $entry->get_value("traffic") || 0;
	print "1. ".$entry->dn.": ";
	$r = $ldap->modify($entry->dn,
		replace => { 'traffic' => '0' }
	);
	print $r->error."\n";
}


$results = $ldap->search (
		base => "o=htlwrn,c=at",
		filter => "(&(uid=*) (inetStatus=2))",
		) or die "$@";
$results->code && die "Error: " . $results->error;
foreach $entry ($results->all_entries) {
	print "2. ".$entry->dn.": ";
	$r = $ldap->modify($entry->dn,
		replace => { 'inetStatus' => '1' }
	);
	print $r->error."\n";
}


$ldap->unbind;


my $dbh = Mysql->connect('10.0.2.100', "sas", "sastraffic", "htlits");
$dbh->query("DELETE FROM sas.trafficlog");

