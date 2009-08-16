#!/usr/bin/perl 
#  Qbe writetraffic 0.01 / Moves MySQL Traffic data to LDAP
#  (C) Copyright 2003 Christian Hofstaedtler
#  $Id: qbe_writetraffic.pl,v 1.4 2002/11/11 13:15:00 ch Exp $
############################

use Carp;
#use diagnostics;
#use English;
use Net::LDAP;
use strict;
use Mysql;

package Qbe::Session::TrafficSave;
$|=1;  # turn off buffering

my $ldap_base = 'o=htlwrn,c=at';
my $ldap_user = 'cn=Root,'.$ldap_base;
my $ldap_pass = 'XXX';

my $dbh = Mysql->connect('10.0.2.100', "sas", "sastraffic", "XXX");

my $ldap = Net::LDAP->new('10.0.2.100') or die "writeacl: $@";

$ldap->bind(	$ldap_user,	
		password => $ldap_pass
	);

my $results = $ldap->search ( base => $ldap_base,
		filter => "(&(!(loggedonHost=0.0.0.0))(loggedonHost=*) )"
	);

$results->code && die "Error: " . $results->error;

##
##

my $sth; my $newtraffic;
#
#$sth = $dbh->query("select sum(traffic) from trafficview where client LIKE \"10.5.%\"");
#	if (defined($sth))
#	{
#		
#	}

##
##


my $entry;
my $loggedonHost; my $oldtraffic; my $traffic; my $r;
foreach $entry ($results->all_entries) { 
        $loggedonHost = $entry->get_value("loggedonHost") || "";
	if (!defined($loggedonHost)) { next; }

	#print "trying: $loggedonHost... ";
	$sth = $dbh->query("SELECT sum(traffic) FROM sas.trafficlog WHERE ip='".$loggedonHost."'");

	if (!defined($sth)) { next; }

	$newtraffic = $sth->fetchrow;
	if (!defined($newtraffic)) { next; }

        $oldtraffic = $entry->get_value("traffic") || 0;
	if (!defined($oldtraffic)) { $oldtraffic = 0; }
	$traffic = $oldtraffic + $newtraffic;

	$r = $ldap->modify($entry->dn, 
		delete => [ 'traffic' ],
		 );
	$r = $ldap->modify($entry->dn,
		add => { 'traffic' => $traffic }
		 );
	#print($traffic."... ");
	$dbh->query("DELETE FROM sas.trafficlog WHERE ip='".$loggedonHost."'");
	#print "next...\n";
}

##
## copy everything left to the trafficip table
##
##$sth = $dbh->query("INSERT IGNORE INTO sas.trafficip SELECT ip,SUM(traffic) as traffic FROM sas.trafficlog GROUP BY ip");
$sth = $dbh->query("CREATE TEMPORARY TABLE newtraffs SELECT trafficlog.ip AS ip, IFNULL( oldip.traffic, 0 ) + SUM( trafficlog.traffic ) AS traffic FROM sas.trafficlog LEFT JOIN sas.trafficip AS oldip ON trafficlog.ip = oldip.ip GROUP BY trafficlog.ip");
#$sth = $dbh->query("DELETE FROM trafficip");
$sth = $dbh->query("REPLACE INTO trafficip SELECT * FROM newtraffs");


##
## now sum up everything left and write it into the nobody user
## 
$sth = $dbh->query("SELECT sum(traffic) FROM sas.trafficlog");
if (defined($sth)) {
	$newtraffic = $sth->fetchrow;
	$dbh->query("DELETE FROM sas.trafficlog");


	$results = $ldap->search ( base => $ldap_base, filter => "(uid=nobody)" );
	$results->code && die "Error: " . $results->error;
	
	#print $results->count()." ";
	#my @entries = $results->entries();
	$entry = $results->pop_entry();

	$oldtraffic = $entry->get_value("traffic") || 0;
	if (!defined($oldtraffic)) { $oldtraffic = 0; }
	$traffic = $oldtraffic + $newtraffic;
	
	$r = $ldap->modify($entry->dn,
		delete => ['traffic']
		);
	$r = $ldap->modify($entry->dn,
		add => { 'traffic' => $traffic }
		);
	
}

# -eof-

