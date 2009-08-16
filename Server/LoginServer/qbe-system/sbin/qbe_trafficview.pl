#!/usr/bin/perl -w
#  Qbe trafficview 0.01
#  (C) Copyright 2002-2003 Christian Hofstaedtler
#  $Id: qbe_dhcpconf.pl,v 1.4 2002/11/11 13:15:00 ch Exp $
############################

use Carp;
use diagnostics;
use English;
use Net::LDAP;
use strict;
use Mysql;

my $app = "trafficview";

print STDERR "Qbe Traffic SQLView\n";
print STDERR "(C) Copyright 2002-2003 Christian Hofstaedtler\n";

package Qbe::Session::TrafficExport;
$|=1;  # turn off buffering

my $dbh = Mysql->connect("localhost", "sas", "sastraffic", "XXX");

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "$app: $@";

$ldap->bind('cn=Root,o=htlwrn,c=at',	
		password => 'XXX'
	) or die "$app: $@";

my @attrs = ('uid','inetStatus');
my $results = $ldap->search (
		base => "o=htlwrn,c=at",
		filter => "(&(uid=*) (!(traffic=0)))"
	) or die "$app: $@";

my $sth = $dbh->query("DELETE FROM sas.trafficview");

$results->code && die "Error: " . $results->error;

my $entry; my $traffic; my $uid; my $xuid; my $xabt; my $tmp; 
foreach $entry ($results->all_entries) { 
        $traffic = $entry->get_value("traffic") || 0;
	$uid = $entry->get_value("uid") || "";

	$xabt = $entry->dn;
	($xuid,$xabt) = split(/,/,$xabt,2);
	$xabt =~ s/ou=//;
	$xabt = substr($xabt,0,3);
	($xabt,$tmp) = split(/,/,$xabt,2);
	if ($uid eq 'nobody') { $xabt = 'no'; }
	$sth = $dbh->query("INSERT INTO sas.trafficview VALUES('".$uid."','".$traffic."','".$xabt."')");
#	print "* $xuid - $xabt\n";
		
}

$ldap->unbind;

