
use Net::LDAP;

$qbe_ldap_server = "localhost";
$qbe_ldap_username = "cn=Root,o=htlwrn,c=at";
$qbe_ldap_password = "XXX";

sub qbe_ldap_make
{
	my $ldap = Net::LDAP->new($qbe_ldap_server) or die "trafficcheck: $@";
	$ldap->bind($qbe_ldap_username,
			password => $qbe_ldap_password
			) or die "$0: $@";
	return $ldap;	
}

