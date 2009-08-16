<?
	header("Content-Type: text/plain");
	require("../sas.inc.php");
	error_reporting(15);

	$ip = (isset($_REQUEST['ip']) ? $_REQUEST['ip'] : "");
	if ($ip == "")
	{	echo "*UNKNOWN*";
		exit;
	}

	$ds = ldap_connect($sas_ldap_server);
	$results = ldap_search($ds,$sas_ldap_base,'(& (objectClass=posixAccount) (loggedonHost='.$ip.'))');
	$arr = ldap_get_entries($ds,$results);
	if ($arr["count"] == 1)
	{
		echo $arr[0]["uid"][0];
		exit;
	} else {
		echo "*UNKNOWN*";
		exit;
	}
	
	var_dump($arr);
	ldap_close($ds);
