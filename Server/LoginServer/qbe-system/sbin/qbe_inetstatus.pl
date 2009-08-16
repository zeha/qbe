#!/usr/bin/perl -w
#  Qbe inetstatus 0.01
#  (C) Copyright 2002-2003 Christian Hofstaedtler
#  $Id: qbe_inetstatus.pl,v 1.0 2002/11/11 13:15:00 ch Exp $
############################

use Carp;
use diagnostics;
use English;
use Net::LDAP;
use strict;

package Qbe::Session::InetStatus;
$|=1;  # turn off buffering

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "trafficcheck: $@";

$ldap->bind('cn=Root,o=htlwrn,c=at',	
		password => 'XXX'
	) or die "trafficcheck: $@";

my $results;
my $entry; my $traffic; my $inet; my $r;

####
#### Die einzelnen User-Objekte zuruecksetzen
####

$results = $ldap->search (
 		base => "ou=Students,ou=People,o=htlwrn,c=at",
		filter => "(&(uid=*) (inetStatus=0) )",
	) or die "$@";

$results->code && die "Error: " . $results->error;

foreach $entry ($results->all_entries) { 
	#print $entry->dn.": ";
	$r = $ldap->modify($entry->dn,
		replace => { 'inetStatus' => '1' }
	);
	#print $r->error."\n";
}

####
#### Die Klassen- und Gruppenobjekte ebenfalls zuruecksetzen
####
$results = $ldap->search (
		base => "ou=Classes,ou=People,o=htlwrn,c=at",
		filter => "(| (inetStatus=0) (inetStatus=-2))",
	) or die "$@";

$results->code && die "Error: " . $results->error;

foreach $entry ($results->all_entries) {
	$r = $ldap->modify($entry->dn,
		replace => { 'inetStatus' => '1' }
	);
}


$ldap->unbind;

