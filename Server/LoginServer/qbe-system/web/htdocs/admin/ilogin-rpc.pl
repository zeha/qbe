#!/usr/bin/perl
#
$sysip = $ARGV[0];

require LWP::UserAgent;
my $ua = LWP::UserAgent->new(env_proxy => 0,
				keep_alive => 0,
				timeout => 30,
				);

$response = $ua->get('http://'.$sysip.':7666/system/getinfo?type=username');

 my $id = $response->content;
 $id =~ s/\n//;
 $id =~ s/\r//;

 print $id;

# "echo " . $response->content . " >> /qbe/log/rpclog";

