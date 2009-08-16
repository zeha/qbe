<?php
////////////////////////////////////////////////////////////////////////////////////
// ldap_auth_ibutton(int ibuttonserial)
// Return: void (keine Rückgabe)
//
//Funktion:
//        Die Function erwartet als Parameter die IButton Seriennummer und gibt bei
//        erfolgreicher Authentifizierung 0 zurück und bei Fehler 1 zurück.
//        Bei erfolgreicher Authentifizierung wird ebenfalls die Session Daten auf den Benutzer gesetzt

function ldap_auth_ibutton($ibutton_serial)
{
    global $dn;
    // Verbindung zum LDAP Aufnehmen
    $ld_con = @ldap_connect("blackbox.htlwrn.ac.at");
    if ($ld_con){
        // Authentifizierung am LDAP um den Besitzer des IButtons zu ermitteln
        $ld_bind = @ldap_bind($ld_con,"ou=Administration,o=htlwrn,c=at","htlits");
        // suchen des Users der zum IButton gehört
        $ld_srch = @ldap_search($ld_con,"ou=people,o=htlwrn,c=at","unique key=$ibutton_serial");
        // Prüfen ob es überhaupt einen Eintrag zu dem IButton gibt
        if (ldap_count_entries($ld_con,$ld_srch)){
            // ersten Eintrag lesen
            $ld_entr = @ldap_first_entry($ld_con,$ld_srch);
            // DN des Eintrages auslesen
            $dn = @ldap_get_dn($ld_con,$ld_entr);
            // Attribute des Eintrages bestimmen
            $ld_attrs = @ldap_get_attributes($ld_con,$ld_entr);
            // 
            if ($ld_attrs["gidNumber"][0]=="201"){
                @ldap_close($ld_con);
                return(0);
            }else{
                @ldap_close($ld_con);
                return(1);
            }
        }else{
            @ldap_close($ld_con);
            return(1);
        }
    }else{
        @header("Location: http://titan.ulisoft.net/sis/test1.php?err=1");
    }
}

/*
function sas_ldap_getdn($uid)
{
	global $sas_ldap_server, $sas_ldap_base;

	$lr = ldap_connect("10.0.2.10");
	if ($lr)
	{
           $r = ldap_bind($lr);
           $sr = ldap_search($lr,"o=htlwrn, c=at","uid=$uid");
           $sr2 = @ldap_first_entry($lr, $sr);
		   $ou = $sr2[0]["ou"][0];
           $dn = @ldap_get_dn($lr,$sr2);
           @ldap_close($lr);

           if ($dn) return $dn; else return false;

	} else
	return false;
}

function sas_ldap_checkpassword($user,$pass)
{
	global $sas_ldap_server;
	$lr = ldap_connect("10.0.2.10");

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
*/

