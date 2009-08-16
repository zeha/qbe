<?
	include "/qbe/status/acl/diskspace";
	if (isset($diskspace[$argv[1]])) 
	{
		echo intval($diskspace[$argv[1]]);
	} else {
		echo "0";
	}
?>
