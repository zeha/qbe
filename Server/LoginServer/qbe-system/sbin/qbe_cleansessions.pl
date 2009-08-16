#!/usr/bin/perl -w
#  Qbe cleansessions 1.00: Terminates Expired Sessions and send new data to alive clients
#  (C) Copyright 2002-2004 Christian Hofstaedtler
#  $Id$
############################

use Carp;
use diagnostics;
use English;
use Net::LDAP;
use strict;

require LWP::UserAgent;


my $max_disk_user = 20;
my $max_disk_teacher = 1000;

my $max_traffic_user = 150;
my $max_traffic_teacher = 1000;

package Qbe::Session::AutoClean;
$|=1;  # turn off buffering

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "writeacl: $@";

$ldap->bind('cn=Root,o=htlwrn,c=at',	
		password => 'XXX'
	);

my $results = $ldap->search ( base => "o=htlwrn,c=at",
		filter => "(&(uid=*) (lastActivity=*) (loggedonHost=*) )"
	);

$results->code && die "Error: " . $results->error;
my @attrs = ( 'lastActivity', 'loggedonHost', 'inetStatus', 'traffic', 'uid' ); #, 'logonTime', 'loggedonHost', 'loggedonMac' );


	open (LOGFILE, ">>/qbe/log/cleansessions.log");
	print LOGFILE ">> new session: ".time()." <<\n";

my $entry; my $execdisk; 
my $diskspaceabs; my $diskspacemax; my $diskspaceper;
my $trafficabs; my $trafficmax; my $trafficper;
my $mytime = time();
my $lastActivity; my $clientRefresh; 
foreach $entry ($results->all_entries) { 
#        $logonTime = $entry->get_value("logonTime") || "";

	my $host = $entry->get_value("loggedonHost") || "";
        
        $lastActivity = $entry->get_value("lastActivity") || "";
	$lastActivity = $lastActivity + (5*60);

	if ($lastActivity < $mytime)
	{	my $r;
		print LOGFILE $host." ".$mytime." ".$lastActivity." ".$entry->dn.":\n  >";
		
		my $ua = LWP::UserAgent->new(env_proxy => 0,
				keep_alive => 0,
				timeout => 30,
			);
		if (!defined($entry->get_value('uid'))) { next; }
		
		my $inetstat = $entry->get_value('inetStatus');
		if ($inetstat == 7) { $inetstat = 0; }

		$execdisk = 'php4 /qbe/sbin/getdiskspace.php '.$entry->get_value('uid');
		$diskspaceabs = `$execdisk`;
		$trafficabs = $entry->get_value('traffic') / (1000*1000);
		
		$diskspacemax = $max_disk_user;
		$trafficmax = $max_traffic_user;
		
		$_ = $entry->dn;
		if (/Teachers/)
		{
			$diskspacemax = $max_disk_teacher;
			$trafficmax = $max_traffic_teacher;
		}
		if (/Administration/)
		{
			$diskspacemax = $max_disk_teacher;
			$trafficmax = $max_traffic_teacher;
		}

		$diskspaceper = sprintf("%.0d",( $diskspaceabs / $diskspacemax) * 100);
		$trafficper = sprintf("%.0d",( $trafficabs / $trafficmax) * 100);

		my $update = 'http://'.$host.':7666/ilogin/statusupdate?event=dataupdate&internet='.$inetstat.
				'&diskspace='.$diskspaceper.'&diskspace_abs='.$diskspaceabs.'&diskspace_max='.$diskspacemax.
				'&traffic='.$trafficper.'&traffic_abs='.$trafficabs.'&traffic_max='.$trafficmax;
		my $response = $ua->get($update);

		#print $update.": ".$response->content;

		if ( substr($response->content, 0, 2) eq 'Ok' )
		{
			my $r = $ldap->modify($entry->dn,
			replace => { 'lastActivity' => $mytime });
			print LOGFILE $r->error . " " . $response->content;
		} else {
			print LOGFILE "*NO-RESPONSE*: " . $response->content;

			$r = $ldap->modify($entry->dn, 
				delete => [ 'lastActivity' ]);
			$r = $ldap->modify($entry->dn,
				delete => [ 'loggedonHost' ]);
			$r = $ldap->modify($entry->dn,
				delete => [ 'loggedonMac' ]);
		}
		print LOGFILE "\n";
	}
}
close LOGFILE;
$ldap->unbind;

