<?
include "../../sas.inc.php";
sas_start("Traffic","../../","/modules/internet",2);
sas_showmenu();

	sas_varimport('class');

        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind( $ldap , $sas_ldap_adminuser, $sas_ldap_adminpass );

	echo "<h3>Klasse $class</h3>\n";

        $l_list = ldap_search( $ldap , $sas_ldap_base ,
			"(&(ou=$class)(objectClass=posixAccount))",
			array("dn","uid","traffic")
		 );
	$topuser = "";
	$toptraffic = 0;

	$totaltraffic = 0;
	$users = 0;
        if ($l_list)
        {       $le = ldap_get_entries( $ldap , $l_list );
		for ($i=0;$i<$le["count"];$i++)
		{
			$users++;
			if (isset($le[$i]["traffic"]))
			{
			if ($le[$i]["traffic"][0] > $toptraffic)
			{	$toptraffic = $le[$i]["traffic"][0];
				$topuser = $le[$i]["uid"][0];
			}
			$totaltraffic = $totaltraffic + $le[$i]["traffic"][0];
			}
		}
	}
	$users++;

	$totaltraffic = intval($totaltraffic / 1024 / 1024);
	echo "Anzahl Benutzer: <b>$users</b><br>";
	echo "Traffic gesamt: <i>$totaltraffic</b> MB</i> (durchschnittlich <i>".
		intval($totaltraffic/$users) . " MB</i> je Benutzer)<br>";
	echo "Benutzer mit dem meisten Einzeltraffic: <b>$topuser</b> (<i>". intval($toptraffic/1024/1024) ." MB</i>)<br>";

sas_end();
?>
