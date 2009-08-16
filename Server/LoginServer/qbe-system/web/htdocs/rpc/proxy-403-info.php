<?
/*
	/qbe/web/htdocs/rpc/proxy-403-info.php
	(C) Copyright 2001-2004 Christian Hofstaedtler
	$Id$
*/
// error_reporting(0);
require("../sas.inc.php");

	function outputImage($string)
	{ global $dn; ?>
	<html><head></head><body style="border: none; padding: 0px 0px 0px 0px; margin: 0px 0px 0px; font-family: Verdana,sans-serif; font-size: 12pt; font-weight: bold; background-color: white; color: red;">
	<span style="color: black;">Status:</span> <?=$string?>
	</body></html>
	<?}

	$user = isset($_GET['uid']) ? $_GET['uid'] : "";
	//$url = isset($_GET['url']) ? $_GET['url'] : "";
	
	$ds = ldap_connect($sas_ldap_server);
	ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	
	//if ($user == "%u") { $user = ""; }
	if ($user == "") {
		$ip = sas_web_getclientip();
		$result = ldap_search($ds,"o=htlwrn,c=at","(loggedonHost=$ip)");
		$entries = ldap_get_entries($ds,$result);
		$dn = '';
		if (isset($entries[0]))
		{
			$dn = $entries[0]['dn'];
		}
	} else {
		$dn = sas_ldap_getdn($user);
	}

	if ($dn != "")
	{
		$result = ldap_read($ds,$dn,"(objectClass=*)");
		$entries = ldap_get_entries($ds,$result);
		$inetstatus = $entries[0]['inetstatus'][0];

		if (!isset($entries[0]['loggedonhost'][0])) { $inetstatus = -1; }
		ldap_close($ds);
		if ($inetstatus == -1)
		{
			outputImage("Sie sind nicht angemeldet!");
		}
		if ( ($inetstatus == 0) || ($inetstatus == 7) )
		{
			outputImage("Ihre Anmeldung wird noch bearbeitet.");
			//"Ihre Anmeldung ist OK, Internet in 1min verfügbar!"); 
			//Sie haben eine verbotene Seite angesurft!");
		}
		if ($inetstatus == 1)
		{
			outputImage("Sie sind nicht berechtigt!");
		}
		if ($inetstatus == 2)
		{
			outputImage("Sie haben Ihr Limit überzogen!");
		}
		if ($inetstatus == 3)
		{
			outputImage("Sie wurden aufgrund von Fehlverhalten gesperrt.");
		}
		if ($inetstatus == 4)
		{
			outputImage("Zu viel Traffic pro Tag!");
		}
		if ($inetstatus == 5)
		{
		        outputImage("Internet für Testumgebung aktiv!");
		}
		if ($inetstatus == 6)
		{
		        outputImage("Internet aufgrund eines laufenden Tests gesperrt!");
		}
		if ($inetstatus == 99)
		{
			outputImage("Du willst gar net wissen warums net geht!!!");
		}
	} else {
	#	header("Location: https://blackbox.htlwrn.ac.at/");
		outputImage("Sie sind nicht angemeldet!");
	}
/* do not add ?>, this is known to cause problems */
