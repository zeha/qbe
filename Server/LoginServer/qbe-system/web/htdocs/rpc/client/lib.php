<?php

function delLoginHost()
{
  global $sas_ldap_server, $sas_ldap_adminuser,$sas_ldap_adminpass,$sas_ldap_base;
  $i = 0;
  $ds = ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	$entries = ldap_search ( $ds , $sas_ldap_base, 'loggedonHost='.sas_web_getclientip() , array('dn') );
	$entries = ldap_get_entries ( $ds, $entries );
/*	?><pre><?=var_dump($entries);?></pre><?  */

	$attrs = array();
	$attrs["loggedonHost"] = array();
	$attrs["loggedonMac"] = array();
	$attrs["lastActivity"] = array();


	for ($i = 0; $i < $entries['count']; $i++)
	{
	   	$r = ldap_modify($ds, $entries[$i]['dn'], $attrs);
		qbe_log_text("qbe-appmodule-client-logout",LOG_NOTICE,"User Logout: \"".sas_web_getclientip()."\" \"".$entries[$i]['dn']."\"");
	}
	
        ldap_close($ds);
  }
  return $i;
}

