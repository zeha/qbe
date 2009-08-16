<?

$xuid = $_GET['uid'];

include "../../sas.inc.php";
// pass 0 as last parameter, so we dont get a header...
sas_start("show_userphoto.php","../../","/tools",2,0);

if (!sas_ldap_isadmin($user))
{	$xuid = $user;	}
 
if ($xuid == "")
{	$xuid = $user;	}

	Header ("Content-type: image/jpeg");

        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

        $l_list = ldap_read( $ldap , $xuid , "objectClass=*" );

	if ($l_list)
	{
	        $l_entry = ldap_first_entry( $ldap , $l_list );
		$a = ldap_get_values_len($ldap, $l_entry, "jpegPhoto");
		echo $a[0];
	}

	ldap_close($ldap);

