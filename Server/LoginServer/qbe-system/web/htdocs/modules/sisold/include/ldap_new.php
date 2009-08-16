<?php
function ldap_auth($user, $pass)
{

function sas_ldap_checkpassword($user,$pass)
{
	global $sas_ldap_server;
	$lr = ldap_connect("10.0.2.100");

	if ($lr)
	{
		$r = @ldap_bind($lr,$user,$pass);
		ldap_close($lr);
		if ($r)
		        return true; 
		else 
		return false;
	} else {
		echo "No Connection to LDAP or user not found!<br/>";
		return false;
	}
}

function sas_ldap_getdn($uid)
{	
	global $sas_ldap_server, $sas_ldap_base;

	$lr = ldap_connect("10.0.2.100");
	if ($lr)
	{
           $r = ldap_bind($lr);
           $sr = ldap_search($lr,"o=htlwrn,c=at","uid=$uid");
           $sr2 = @ldap_first_entry($lr, $sr);
           $dn = @ldap_get_dn($lr,$sr2);
           @ldap_close($lr);

           if ($dn) return $dn; else return false;

	} else
	return false;
}

$dn = sas_ldap_getdn($user);

$check = sas_ldap_checkpassword($dn, $pass);

if ($check == True) {

$start = "";

$laenge = 1;

$erg = substr($user, $start,$laenge);

switch($erg)
{
	case "e": $abt = 1;
	break;
	case "a": $abt = 2;
	break;
	case "h": $abt = 4;
	break;
}

$right = "av";

$userdata = array('user'=>$user, 'pw'=>$pass, 'valid'=>1, 'rights'=>$right, 'id'=>session_id(), 'login_t'=>time(), 'abteilung'=>$abt);

} else {

$userdata = array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());

$_SESSION['userdata'] = $userdata;
}}?>