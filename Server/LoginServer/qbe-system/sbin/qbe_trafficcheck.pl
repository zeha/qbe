#!/usr/bin/perl -w
#  Qbe trafficcheck 0.01
#  (C) Copyright 2002-2003 Christian Hofstaedtler
#  $Id: qbe_dhcpconf.pl,v 1.4 2002/11/11 13:15:00 ch Exp $
############################

push @INC, "/qbe/etc/perl";
require qbesystemconfig;

use Carp;
use diagnostics;
use English;
use Net::LDAP;

$|=1;  # turn off buffering

my $ldap = Net::LDAP->new("localhost") or die "trafficcheck: $@";

$ldap->bind("cn=Root,o=htlwrn,c=at",
		password => "XXX"
	) or die "trafficcheck: $@";
#my $ldap = qbe_make_ldap(); 

my @attrs = ('uid','inetStatus');
my $results = $ldap->search (
		base => "ou=Students,ou=People,o=htlwrn,c=at",
		filter => "(&(uid=*) (!(traffic=0)) (!(inetStatus=2)) (| (inetStatus=0) (inetStatus=1) (inetStatus=7) ) )",
#		attrs => @attrs
	) or die "$@";

$results->code && die "Error: " . $results->error;

my $entry; my $traffic; my $inet; my $r;
foreach $entry ($results->all_entries) {
        $traffic = $entry->get_value("traffic") || 0;
	$inet = $entry->get_value("inetstatus");
	### for DEBUGGING
	###print $entry->dn . ': '.$inet.' - '.$traffic."\n";
	if (($traffic > 150000000) && ($inet < 3))
	{
		print "Traffic more than 150MB: ".$entry->dn."\n";
		$r = $ldap->modify($entry->dn,
			replace => { 'inetStatus' => '2' }
			);
	}
	if (($traffic > 150000000) && ($inet == 7))
	{
		print "Traffic more than 150MB/Projektfreischaltung: ".$entry->dn."\n";
		$r = $ldap->modify($entry->dn,
			replace => { 'inetStatus' => '2' }
			);
	}
}

$ldap->unbind;

