<?
// Seitenstart fuer SAS
include "../../sas.inc.php";
sas_start("Speichern...","../../","/modules/internet",1);
sas_showmenu();
// Zugriffskontrolle ueber die Gruppe "inetlock"
if (!sas_ldap_isgroupmember("inetlock",$user))
{
	echo "Unauthorized Access!<br>";
	sas_end();
	exit;
}

// Benoetigte HTTP GET Variablen in den Namespace importieren
$list = (isset($_GET['list']) ? $_GET['list'] : "root");
$class = (isset($_GET['class']) ? $_GET['class'] : "");
$group = (isset($_GET['group']) ? $_GET['group'] : "");
$status = (isset($_GET['status']) ? $_GET['status'] : "");

if ($status != 0) {
	if ($status != 1) {
		echo "Ungueltige Parameter!"; exit;
	}
}	

// Internetfreigabe/-sperre mitprotokollieren
//$loguid = str_replace(" ","",$user);
$loguser = sas_ldap_getuid($user);
/*$loguserrr = sas_ldap_getusername($loguser);
$loggroup = $group; if ($loggroup == "") { $loggroup = "ganze Klasse"; }
$logstr = "echo $sas_client_ip;$loguserrr;$loguid;$class;$loggroup;".strftime("%D;%T").";".(($status==1)?"gesperrt":"freigegeben");
$execstr = escapeshellcmd($logstr) . " >> /sas/log/inetsave.log";
system($execstr);
*/
	$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
	mysql_select_db($sas_mysql_database,$db);
	$sql = "INSERT INTO inetsavelog (class,classgroup,userdn,userip,saved,newstate) VALUES ('$class','$group','$loguser','".$sas_client_ip."',NOW(),$status)";
	echo $sql.'<br>';
	mysql_query($sql);
	echo mysql_error();
	mysql_close($db);

	qbe_log_text("qbe-appmodule-internet-save",LOG_NOTICE,"Internet Status change ($status): $class/$group by $userid at $sas_client_ip");

// Zum LDAP Verbinden und mit dem Administratorbenutzer anmelden
$ds = ldap_connect($sas_ldap_server);
ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);

echo "<form action=\"$PHP_SELF\" method=get>";
switch($list)
{
 case "class":	// Ganze Klasse sperren
 		$attrs = array();
//		$attrs["inetstatus"][] = $status;
//		$attrs["objectClass"][] = "QbeOrganizationalUnit";
//		$attrs["objectClass"][] = "organizationalUnit";
//		ldap_modify($ds,"ou=".$class.",ou=Classes,ou=People,o=htlwrn,c=at",$attrs);

// gemoved

		// Alle Gruppenobjekte auf den neuen Status setzen
		$sr=ldap_list($ds,"ou=$class,ou=Classes,ou=People,o=htlwrn,c=at","(objectClass=groupOfNames)",array("inetstatus"));
		$entries = ldap_get_entries($ds,$sr);
		$count = $entries["count"];
		if ($count)
		{	echo "Speichere gruppen...<br>\n";
			for ($i = 0; $i < $count; $i++)
			{
//				if (isset($entries[$i]["inetstatus"]))
				{
//					if ($entries[$i]["inetstatus"][0] != $status)
					{
					echo "-> $status ... ";
						$attrs = array();
						$attrs["inetstatus"][] = $status;
						$attrs["objectClass"][] = "QbeGroup";
						$attrs["objectClass"][] = "groupOfNames";
						ldap_modify($ds,$entries[$i]["dn"],$attrs);
						echo "Gruppe: ".$entries[$i]["dn"]."<br>";
					}
				}
			}
		} else { echo "No groups to save!<br>\n"; }

		$attrs = array();
		$attrs["inetstatus"][] = $status;
//		$attrs["objectClass"][] = "QbeOrganizationalUnit";
//		$attrs["objectClass"][] = "organizationalUnit";
		ldap_modify($ds,"ou=".$class.",ou=Classes,ou=People,o=htlwrn,c=at",$attrs);

// moveend

		// Alle Userobjekte fuer die Klasse $class auflisten und den InternetStatus neu setzen
		$sr=ldap_search($ds,"ou=Students,ou=People,o=htlwrn,c=at","(ou=$class)",array("inetStatus","loggedonHost"));
		$entries = ldap_get_entries($ds,$sr);
		$count = $entries["count"];
		for ($i = 0; $i < $count; $i++)
		{	$oldinet = $entries[$i]["inetstatus"][0];	
			#$groups[$i] = substr(strchr($groups[$i],"-"),1);
			$attr = array();
			if (($oldinet == 1) && ($status == 0))
			{$attr["inetStatus"] = 0;}
			if (($oldinet == 0) && ($status == 1))
			{$attr["inetStatus"] = 1;}
			if (isset($attr["inetStatus"]))
			{ldap_modify($ds,$entries[$i]["dn"],$attr);}
			if (isset($entries[$i]["loggedonhost"][0]))
			{	// Benutzer ist im Moment angemeldet, acl-cache updaten...
				echo "ip: ".$entries[$i]["loggedonhost"][0]."<br>\n";
#				writeacl($entries[$i]["loggedonhost"][0],$status);
			}
		}

	?>
	Fertig. (<a href="index.php">Zur&uuml;ck</a>)
	<script>
	<!--//
	// alert("Fertig, <?=$count?> User wurden geaendert!"); 
	location.href="index.php";
	// -->
	</script>
	<?
	break;
 case "group":	// Gruppenobjekt und Benutzer der Gruppe neu setzen 
	$attrs = array();
	$attrs["inetstatus"][] = $status;
	$attrs["objectClass"][] = "QbeGroup";
	$attrs["objectClass"][] = "groupOfNames";
	ldap_modify($ds,"cn=$class-$group,ou=$class,ou=Classes,ou=People,o=htlwrn,c=at",$attrs);

	// Gruppenobjekt einlesen ... 
	$group = ldap_read($ds,"cn=$class-$group,ou=$class,ou=Classes,ou=People,o=htlwrn,c=at","(objectClass=groupOfNames)",
		array("member"));
	$members = ldap_get_entries($ds,$group);
	$membercount = $members[0]["member"]["count"];
	echo $membercount-1 . " User werden geaendert...<br>\n";
	for ($thismember = 0; $thismember < $membercount; $thismember++)
	{
		$memberuid = $members[0]["member"][$thismember];
		$isclass = 0;
		$isclass = (strstr($memberuid,"ou=Classes") != "");
		if (!$isclass)
		{
			// Benutzerobjekt aus dem Gruppenobjekt auslesen, inetstatus neu setzen
			$sr = ldap_read($ds,$memberuid,"(ou=$class)",array("inetStatus"));
			$entries = ldap_get_entries($ds,$sr);
			$oldinet = $entries[0]["inetstatus"][0];
			$attr = array();
		        if (($oldinet == 1) && ($status == 0))
		        {$attr["inetStatus"] = 0;}
		        if (($oldinet == 0) && ($status == 1))
		        {$attr["inetStatus"] = 1;}
		        if (isset($attr["inetStatus"]))
		        {ldap_modify($ds,$memberuid,$attr);}
			if (isset($entries[0]["loggedonhost"][0]))
			{	// Benutzer ist im Moment angemeldet, acl-cache updaten
				echo "ip: ".$entries[0]["loggedonhost"][0]."<br>\n";
#				writeacl($entries[0]["loggedonhost"][0],$status);
			}
		}
	}

	// indicate that the state is inconsistent - a group has been enabled/disabled, but other users may be disabled/enabled
	$attrs = array();
	$attrs["inetstatus"][] = -2;
	$attrs["objectClass"][] = "QbeOrganizationalUnit";
	$attrs["objectClass"][] = "organizationalUnit";
	ldap_modify($ds,"ou=".$class.",ou=Classes,ou=People,o=htlwrn,c=at",$attrs);
	?>
	Fertig. (<a href="index.php?list=class&class=<?=$class?>">Zur&uuml;ck</a>)
	<script>
	<!--//
	// alert("Fertig, <? echo $membercount-1?> Benutzer wurden geaendert!"); 
	location.href="index.php?list=class&class=<?=$class?>";
	// -->
	</script>
	<?
	break;
 default:
	echo "Invalid Arguments!";
	break;
}

// Seitenende
ldap_close($ds);
echo "</form>";

sas_end();
?>
