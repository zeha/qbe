#!/usr/bin/perl
#
#

open FILE, ">/qbe/status/acl/diskspace";
print FILE '<?php '."\n";

opendir(homedirs,"/import/homes/");
@dirs = readdir(homedirs);
foreach $file (@dirs)
{
	if ($file eq '.') { next; }
	if ($file eq '..') { next; }
	if ($file eq '.status') { next; }
	$_ = `du -sxhm /import/homes/$file`;
	($space,$nix) = split(/\t/);
	print FILE "\$diskspace\['".lc($file)."'\] = \"$space\";\n";
#	print "\$diskspace\['".lc($file)."'\] = \"$space\";\n";
}
closedir(homedirs);

print FILE '?>';
close FILE;

