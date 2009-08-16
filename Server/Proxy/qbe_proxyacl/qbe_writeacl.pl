#!/usr/bin/perl -w
#  Qbe proxyacl/writeacl 0.01
#  (C) Copyright 2002 Christian Hofstaedtler
#  $Id: qbe_writeacl.pl,v 1.4 2002/11/14 13:47:30 ch Exp $
############################
use Carp;
use diagnostics;
use English;
use Net::LDAP;
use strict;

print STDERR "Qbe writeacl.pl 0.01 / LDAP ACL for squid\n";
print STDERR "(C) Copyright 2002 Christian Hofstaedtler\n";

package Qbe::Proxy::ACL::write;
$|=1;  # turn off buffering

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "writeacl: $@";
$ldap->bind('ou=Administration,o=htlwrn,c=at',	
	password => 'htlits'
	);

my $results = $ldap->search ( base => "o=htlwrn,c=at",
			filter => "(&(inetStatus=0)(loggedonHost=*))"
		);

$results->code && die $results->error;

my $entry;
my $username; my $logonHost; my $mac; my $entryok;
foreach $entry ($results->all_entries) { 
	$entryok = 1;
        $username = $entry->get_value("uid") || "";
        if ($username eq "") { $entryok = 0; }
        $logonHost = $entry->get_value("loggedonHost") || "";
        if ($logonHost eq "") { $entryok = 0; }
        $mac = $entry->get_value("loggedonMac") || "";
        if ($mac eq "") { $entryok = 0; }

	if ($entryok == 1)
	{
		print $username . " " . $logonHost . " " . $mac . "\n";		
	}
}

print "\n";	# print newline so qbe_writeacl terminates....
$ldap->unbind;

