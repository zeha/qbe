<?
     function lookuplogonip($ip) {
	global $sas_ldap_machineuser,$sas_ldap_machinepass,$sas_ldap_server;
	global $sas_ldap_base;
        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind($ldap,$sas_ldap_machineuser,$sas_ldap_machinepass);

        $sr = ldap_search($ldap,$sas_ldap_base,"(loggedonHost=$ip)");
        $usrinfo = ldap_get_entries($ldap, $sr);
        if (isset($usrinfo[0]["uid"]))
        { return $usrinfo[0]["uid"][0];
        } else { return ""; }
	ldap_close($ldap);
     }
     function lookuphostip($ip) {
	global $sas_ldap_machineuser,$sas_ldap_machinepass,$sas_ldap_server;
	global $sas_ldap_base;
        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind($ldap,$sas_ldap_machineuser,$sas_ldap_machinepass);

         $sr = ldap_search($ldap,$sas_ldap_base,"(ipHostNumber=$ip)");
         $usrinfo = ldap_get_entries($ldap, $sr);
         if (isset($usrinfo[0]["uid"]))
         { return $usrinfo[0]["uid"][0];
         } else { return ""; }

	ldap_close($ldap);
     }
     function lookupmac($mac) {
	global $sas_ldap_machineuser,$sas_ldap_machinepass,$sas_ldap_server;
	global $sas_ldap_base;
        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind($ldap,$sas_ldap_machineuser,$sas_ldap_machinepass);

        $sr = ldap_search($ldap,$sas_ldap_base,"(macAddress=$mac)");
        $usrinfo = ldap_get_entries($ldap, $sr);
        if (isset($usrinfo[0]["uid"]))
        { return $usrinfo[0]["uid"][0];
        } else {
	//	if (isset($usr))
	 return "";
	}
	ldap_close($ldap);
     }
?>