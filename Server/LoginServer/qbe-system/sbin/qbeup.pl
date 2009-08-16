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

print STDERR "Qbe trafficclean 0.01 / cleans traffic\n";
print STDERR "(C) Copyright 2002-2003 Christian Hofstaedtler\n";

package Qbe::Session::InetStatus;
$|=1;  # turn off buffering

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "trafficcheck: $@";

$ldap->bind('cn=Root,o=htlwrn,c=at',	
		password => 'XXX'
	) or die "trafficcheck: $@";

my $results = $ldap->search (
		base => "ou=Teachers,ou=People,o=htlwrn,c=at",
		filter => "(&(uid=*) (inetStatus=1) )",
	) or die "$@";

$results->code && die "Error: " . $results->error;

my $entry; my $traffic; my $inet; my $r;
foreach $entry ($results->all_entries) { 
	print $entry->dn.": ";
	$r = $ldap->modify($entry->dn,
		replace => { 'inetStatus' => '0' }
	);
	print $r->error."\n";
}

$ldap->unbind;

