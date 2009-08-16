#!/usr/bin/perl -w
#  Qbe dhcpconf 1.00
#  (C) Copyright 2002-2004 Christian Hofstaedtler
#  $Id$
############################

use diagnostics;
use English;
use Net::LDAP;

print STDERR "Qbe dhcpconf 1.00 / LDAP DHCP Auto-Config\n";
print STDERR "(C) Copyright 2002-2004 Christian Hofstaedtler\n";

package Qbe::DHCP::AutoConf;
$|=1;  # turn off buffering

my $globalbase = "o=htlwrn,c=at";

sub searchLDAP($$)
{
	my $base = shift(@_);
	my $scope = shift(@_);

my $ldap = Net::LDAP->new('blackbox.htlwrn.ac.at') or die "writeacl: $@";
$ldap->bind('cn=Administrator,ou=Administration,o=htlwrn,c=at',
	password => 'XXX') or die "$@";

my $results = $ldap->search ( base => $base,
			filter => "(&(!(ipHostNumber=0.0.0.0)) (objectClass=qbeIpDevice))",
			scope => $scope
		);

$results->code && die $results->error;

my $entry; my $count = 0;
my $username; my $ipHost; my $mac; my $entryok;
foreach $entry ($results->all_entries) { 
	$count++;
	$entryok = 1;
        $username = $entry->get_value("uid") || "";
        if ($username eq "") { $entryok = 0; }
        $ipHost = $entry->get_value("ipHostNumber") || "";
        if ($ipHost eq "") { $entryok = 0; }
	if ($ipHost eq "0.0.0.0") { $entryok = 0; }
        $mac = $entry->get_value("macAddress") || "";
        if ($mac eq "") { $entryok = 0; }
	if ($mac eq "00:00:00:00:00:00") { $entryok = 0; }
	
	if ($entryok == 1)
	{	if ($username eq "ch") { $username = "pandora"; }
		if ($username eq "as") { $username = "sysmaster"; }
		$username =~ s/\$//;
		my $domain = ""; $_ = $entry->dn;
		if (!defined($username)) { print STDERR "NO USERNAME\n"; }
		if (!defined($mac)) { print STDERR "NO MAC\n"; }
		if (!defined($ipHost)) { print STDERR "NO IPHOST\n"; }
		if (!defined($domain)) { print STDERR "NO DOMAIN\n"; }
		print "host $username { hardware ethernet $mac; fixed-address $ipHost; ddns-domainname \"".$domain."htlwrn.ac.at\"; }\n";
	} else {
		print "# not adding $username\n";
	}
}

	print "# $count entries for $base ($scope)\n";
	print "\n";	# print empty line so that our write program terminates ....
	$ldap->unbind;

}

searchLDAP("ou=Administration,".$globalbase,"one");
searchLDAP("ou=Hosts,ou=Administration,".$globalbase,"sub");
searchLDAP("ou=Teachers,ou=People,".$globalbase,"sub");
searchLDAP("ou=Students,ou=People,".$globalbase,"sub");
searchLDAP("$globalbase","one");

