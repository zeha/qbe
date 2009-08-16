<?
// Seitenstart fuer SAS
require "../../sas.inc.php";
sas_start("Internetzugang: PC Only","../../","/modules/internet",1);
sas_showmenu();

// Zugangskontrolle ueber Gruppe "inetrawpc"
if (!sas_ldap_isgroupmember("inetrawpc",$user))
{	// Kein Zugriff,  beenden...
	echo "Unauthorized Access!<br>";
	sas_end();
	exit;
}

// HTTP POST Variablen in den Namespace importieren
$xpc = (isset($_POST['xpc']) ? $_POST['xpc'] : "");

// Zum LDAP Verbinden
$ds = ldap_connect($sas_ldap_server);
ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);

// Parameter wurden uebergeben, Auswahl speichern....
if ($xpc != "")
{	// Parameter kommt als doppelt assoziatives Array...
	foreach($xpc as $pcname => $status)
	{	// neuen Status holen
		$status = substr($status,0,1);
		$objdn = "uid=".$pcname.",ou=hosts,ou=Administration,".$sas_ldap_base;
		$attr = array();
		$attr["inetstatus"][0] = $status;
		ldap_modify($ds,$objdn,$attr);	//Speichern
		// IP des Rechners auslesen und in den acl-cache eintragen
		$rs = ldap_read($ds,$objdn,"(objectClass=*)");
		$entry = ldap_get_entries($ds,$rs);
		if ($status == 1) { $status = -1; }
#		writeacl($entry[0]["iphostnumber"][0],$status);
	}
}

listit();

// Alle dem eigenen Benutzerobjekt zugeordneten PCs auflisten
function listit()
{	global $ds, $sas_ldap_base, $user, $PHP_SELF;
	?>
	<form method=post action="<?=$PHP_SELF?>">
	<? qbe_web_maketable(true); ?>
	<tr><th>PC Name</th><th>Ein</th><th>Aus</th><th>IP</th><th>Aktuell</th></tr>
	<?	// pcs suchen
	$rs = ldap_search($ds,"ou=hosts,ou=Administration,".$sas_ldap_base,"(owner=".$user.")");
	$entries = ldap_get_entries($ds,$rs);
	if ($entries["count"])
	{for ($i=0;$i<$entries["count"];$i++)
	{
		$inet = (isset($entries[$i]["inetstatus"][0])) ? ($entries[$i]["inetstatus"][0]) : 1;
		qbe_web_maketr();
		?>
		<td><?=$entries[$i]["uid"][0]?></td>
		    <td><input class="borderoff" type="radio" name="xpc[<?=$entries[$i]["uid"][0]?>]" value="0" style="height: 15px;"></td>
		    <td><input class="borderoff" type="radio" name="xpc[<?=$entries[$i]["uid"][0]?>]" value="1" CHECKED style="height: 15px;"></td>
		    <td><?=$entries[$i]["iphostnumber"][0]?></td>
		    <td><? if ($inet==1) { echo "off"; } else { echo "on"; }?></td>
		</tr>
		<?
	}}
	?>
	<tr><td colspan=5 align=right><button type=submit>Speichern</button></td></tr>
	</table></form>
	<?
}

// Seitenende
ldap_close($ds);
sas_end();
?>
