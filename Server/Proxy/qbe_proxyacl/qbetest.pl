#!/usr/bin/perl -w

while (<STDIN>)
{
open LOG, ">>/tmp/qbetest";
	print LOG $_;
close LOG;
	print STDOUT "OK\n";
}

#exit;
