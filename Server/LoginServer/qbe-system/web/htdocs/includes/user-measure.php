<?
     require_once 'user-info.php';
     function gettraffic($userid) {
if (!isset($userid)) { exit; }
if ($userid == "") { exit; }
	global $sas_ldap_machineuser,$sas_ldap_machinepass,$sas_ldap_server;
	global $sas_ldap_base;
        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind($ldap,$sas_ldap_machineuser,$sas_ldap_machinepass);

        $sr = ldap_search($ldap,$sas_ldap_base,"(uid=$userid)");
        $usrinfo = ldap_get_entries($ldap, $sr);

        if (isset($usrinfo[0]["traffic"]))
        { 	$trafficabsolute = intval($usrinfo[0]["traffic"][0] / 1000 / 1000) + 1;
#		$trafficpercent = intval(($trafficabsolute / 150) * 100);
		return $trafficabsolute;
        } else {
		return "0";
	}
	ldap_close($ldap);
     }
     function getdiskspace($userid)
     {
	include('/qbe/status/acl/diskspace');
	if (isset($diskspace[strtolower($userid)]))
	{
		$diskabsolute = $diskspace[strtolower($userid)];
		$diskabsolute = intval($diskabsolute);
	} else {
		$diskabsolute = 0;
	}
#	$diskpercent = intval(($diskabsolute / 20) * 100);
	return $diskabsolute;
     }

	function getdiskspacepercent($userid)
	{
		return getdiskspace($userid) * 20 / 100;
	}
	function gettrafficpercent($userid)
	{
		return gettraffic($userid) * 150 / 100;
	}
