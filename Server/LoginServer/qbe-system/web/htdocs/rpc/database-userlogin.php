<?
	header("Content-Type: text/plain");
	require("../sas.inc.php");
	error_reporting(15);

	$ip = (isset($_REQUEST['ip']) ? $_REQUEST['ip'] : "");
	if ($ip == "")
	{	echo "*UNKNOWN*";
		exit;
	}
	$group = (isset($_REQUEST['group']) ? $_REQUEST['group'] : '');

	$ds = ldap_connect($sas_ldap_server);
	$results = ldap_search($ds,$sas_ldap_base,'(& (objectClass=posixAccount) (loggedonHost='.$ip.'))');
	$arr = ldap_get_entries($ds,$results);
	if ($arr["count"] == 1)
	{
		$dn = $arr[0]['dn']; $uid = $arr[0]['uid'][0];
		if ($group == '')
		{
			echo $uid;
		} else {
			$results = ldap_search($ds,$sas_ldap_base,'(& (objectClass=groupOfNames) (member='.$dn.') (cn='.$group.'))');
			$arr = ldap_get_entries($ds,$results);
			if ($arr['count'] == 1)
			{
				echo $uid;
			} else {
				echo '*UNAUTHORIZED*';
			}
		}
	} else {
		echo "*UNKNOWN*";
	}
	
	//echo "\n";var_dump($arr);
	ldap_close($ds);
