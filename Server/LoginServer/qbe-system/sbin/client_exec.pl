#!/usr/bin/perl -w
#

$sysip = $ARGV[0];
$url = $ARGV[1];

#print "Installing: $share on $sysip<br>\n";

require LWP::UserAgent;
my $ua = LWP::UserAgent->new(env_proxy => 0,
	keep_alive => 0,
	timeout => 30,
	);
	$exec = 'http://'.$sysip.':7666/system/exec?'.$url;
	print $exec."<br>\n";
	$response = $ua->get($exec);
	print $response->content;
												
#echo GET /system/exec?rundll32.exe%20printui.dll,PrintUIEntry%20/in%20/n\\\\htl-e\\et-lexmark HTTP/1.0 >/tmp/fil0r2
#rundll32 printui.dll,PrintUIEntry /ia /b "ET-Lexmark" /f "%windir%\inf\ntprint.inf" /m "Lexmark Optra R Plus Series"
#rundll32 printui.dll,PrintUIEntry /in /n\\htl-e\et-lexmark

