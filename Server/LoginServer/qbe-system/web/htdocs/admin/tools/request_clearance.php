<?
include "../../sas.inc.php";
sas_start("Laptop Freischaltung","../../","/tools",1);
sas_showmenu();

$searchbase = "ou=People,".$sas_ldap_base;
$ldap = ldap_connect($sas_ldap_server);
if (sas_ldap_isadmin($user))
{/*
	?>
	<a href="<?=$PHP_SELF?>?action=listopen">Offene auflisten</a> | */
	?>
	<a href="<?=$PHP_SELF?>?action=listall">Alle auflisten</a> |
	<a href="<?=$PHP_SELF?>">Eigene</a>
	<br/>
	<br/>
<?}

ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

if (isset($_GET['action']))
  $action = $_GET['action'];
  else
  $action = "";

if ($action == "")
{
  echo '<br><br>';
  $sr = ldap_read( $ldap , $user , "(macAddress=*)" );

  if ($sr)
  {
    $info = ldap_get_entries($ldap, $sr);
    if ($info["count"] > 0)
    {
	$hour = date('h');
	$minute = date('m');
	if ($minute >= 0) { $minute = 10; }
	if ($minute >= 10) { $minute = 20; }
	if ($minute >= 20) { $minute = 30; }
	if ($minute >= 30) { $minute = 40; }
	if ($minute >= 40) { $minute = 50; }
	if ($minute >= 50) { $minute = 0; $hour++; }
    	?>
	Sie haben bereits eine MAC Adresse eingetragen: <b><?=$info[0]["macaddress"][0]?></b><br/>
	Der DHCP Server wird Ihnen eine Adresse zuweisen. &Auml;nderungen werden alle 10 Minuten &uuml;bernommen. N&auml;chster Reload: <?=$hour?>:<?=sprintf("%02d",$minute)?>.<br/>
	<br/>
	<a href="<?=$PHP_SELF?>?action=delete">MAC Adresse l&ouml;schen</a> - Dies entfernt Ihre alte MAC Adresse aus dem System und Sie k&ouml;nnen eine neue Adresse eintragen.<br/>
	<br/>
	<br/>
	<?
	/*if (!isset($info[0]["l"])) { $info[0]["l"][0]="0"; }
		if ($info[0]["l"][0][0] == 0) { ?>Ihre Anfrage wurde noch nicht bearbeitet.
			<a href="<?=$PHP_SELF?>?action=delete">L&ouml;schen</a> <? }
		if ($info[0]["l"][0][0] == 1) { ?>Ihre IP-Adresse: <b><?=$info[0]["iphostnumber"][0]?></b><? }
		if ($info[0]["l"][0][0] == 2) { ?>Ihre Anfrage wurde abgelehnt. Informationen: <b><?=$info[0]["l"][0][0]?></b> 
    	} */
	
 } else {
  if (sas_web_getclientip())
  $clientmac = sas_web_getclientmac(sas_web_getclientip());
  else
  $clientmac = "";
  ?>
	<h3>Neue Freischaltung beantragen</h3>
	<form action="<?=$PHP_SELF?>" method="GET">

	
		MAC Adresse:<br>
		<input type="text" name="mac" value="<?=$clientmac?>" /> <? sas_makehelplink('macaddr'); ?><br>
		<br>
		<button type="submit">Absenden</button>
		<input type="hidden" name="action" value="new" />
	</form>
	<br/><br/>
	<br/>
	
  <?
 }
}
}
if ($action == 'new')
{
	$req_mac = $_GET['mac'];
	if (!qbe_validate_mac($req_mac)) { $req_mac=''; }
	
	if (strlen($req_mac) == 17)
	{
	 $req_mac = str_replace("-", ":", $req_mac);

	 $info = array();
	 $info["objectClass"] = "ieee802device";
	 $result = @ldap_mod_add($ldap,$user,$info);
	 $error = ldap_error($ldap);
	 if (!$result) {
	 	if ($error != "Type or value exists")
		{ print "ieee802: ".ldap_error($ldap)."<br>"; }
	 }

	 $info = array();
	 $info["objectClass"] = "ipHost";
	 $result = @ldap_mod_add($ldap,$user,$info);
	 $error = ldap_error($ldap);
	 if (!$result) {
	 	if ($error != "Type or value exists")
		{ print "ipHost: ".ldap_error($ldap)."<br>"; }
	 }
	 
	 $info = array();
	 $info["macaddress"] = $req_mac;
	 $info["ipHostNumber"] = "0.0.0.0";
	 $result = ldap_modify($ldap,$user,$info);
	 if ($result)
	 {
		?>Zur Freischaltung angemeldet!<br/>
			<a href="<?=$PHP_SELF?>">Zur&uuml;ck!</a>
		<?
	 } else {
		?><span class="error">Ein Fehler ist aufgetreten!</span><br/>
		<?
	 }
	} else 	{
	 ?><span class="error">Keine/Ung&uuml;ltige MAC Adresse eingegeben!</span><br/><?
	}
}
if ($action == 'delete')
{
 $info = array();
 $info["macaddress"] = array();
 $result = ldap_modify($ldap,$user,$info);
 if ($result)
	echo "Ausgefuehrt.";
	else
	echo "<span class=\"error\">Fehler </span>";
}

if ($action == 'update') {
  if (sas_ldap_isadmin($user)) {
	$uid = $_GET['id'];
	$n_status = 1; //intval($_GET['status']);
	$n_comment = $_GET['comments'];
	$n_ip = $_GET['ip'];
	$uid = sas_ldap_getdn($uid);
	$abteilung = substr($uid,4,1);
	$subip = 0;

	if (($uid != "") && ($n_ip != ""))
	{

/*	if ($n_status == 1)
	{
		$request = 'SELECT ip from sas.clearance WHERE username="' . substr($uid,0,10) . '"';
		$result = mysql_query($request);
		$row = mysql_fetch_row($res);
		if ($row[0] != "")
		{

		$n_ip = '10.10.';
		if ($abteilung == 'e') $n_ip = $n_ip . '1';
		if ($abteilung == 'h') $n_ip = $n_ip . '2';
		if ($abteilung == 'a') $n_ip = $n_ip . '3';
		if ($abteilung == 'f') $n_ip = $n_ip . '5';
		if ($abteilung == 'w') $n_ip = $n_ip . '6';
		$request = 'SELECT * from sas.clearance WHERE username="max_' . $abteilung . '"';
		$result = mysql_query($request,$sql);
		$row = mysql_fetch_row($result);
		$subip = intval($row[4]) + 1;
		$n_ip = $n_ip . '.' . $subip;
		echo("Neue IP: $n_ip<br/>");
		
		$request = 'UPDATE sas.clearance SET ' .
			'ip=' . $subip . 
			' WHERE username="max_' . $abteilung . '"';
		mysql_query($request);

		}

	}

		$request = 'UPDATE sas.clearance SET ' .
			'status=' . $n_status . ',' .
			'status_comments="' . $n_comment . '",' .
			'ip="' . $n_ip . '"' .
			' WHERE username="' . substr($uid,0,10) . '"';
		mysql_query($request);
		update_dhcp_config();
*/	
	 $info = array();
	 $info["ipHostNumber"][0] = $n_ip;
	 $info["l"][0] = $n_status . ", " . $n_comment;
	 $result = ldap_modify($ldap,$uid,$info);
		echo ldap_error($ldap);
		echo "<br/>Gespeichert.<br/>";
		if (ldap_error($ldap) == "Success") {
		?>	<script>
			history.back(-1);
			</script>
		<?
		}
	}
}}

if (sas_ldap_isadmin($user))
{

/*
	if ($action == 'listopen')
	{
		printlist("(& (macAddress=*) (!(l=1*)) )",true);
	} */
	if ($action == 'listall')
	{
		printlist("(macAddress=*)",false);
	}
}	
        
function printlist($filter,$onlyopen)
{
	global $searchbase,$ldap,$PHP_SELF,$qbe_report_templates;
        $sr = ldap_search($ldap,$searchbase,$filter,array('l','uid','macAddress','ipHostNumber'),0,500);
        if ($sr)
        {	qbe_web_maketable(true);
		?>
                <tr><th>User ID</th>
                <th>MAC</th>
                <th>Legacy IP Adresse</th>
                <!-- <th>Status</th> -->
                <th>Comments</th><th></th></tr>
		<?
		
		$info = ldap_get_entries($ldap, $sr);
                if (!($info["count"] < 1)) 
		{
			for ($i=0; $i<$info["count"]; $i++)
			{
				if (!isset($info[$i]["l"])) { $info[$i]["l"][0] = "0"; }
				if (!isset($info[$i]["uid"])) { continue; }
				if ($onlyopen) { if ($info[$i]["l"][0] > 0) { continue; }}
				qbe_web_maketr();
				?>

				<form action="<?=$PHP_SELF?>" method="GET">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="id" value="<?=$info[$i]["uid"][0]?>" />
				<td><a href="<?=$qbe_report_templates['user-by-uid']?><?=$info[$i]["uid"][0]?>" name="<?=$info[$i]["uid"][0]?>"><?=$info[$i]["uid"][0]?></a></td>
				<td><?=$info[$i]["macaddress"][0]?></td>
				<td><input name="ip" value="<?=$info[$i]["iphostnumber"][0]?>" maxlength="15" size="10" /></td>
<!--				<td>
					<input name="status" type=text value="<?=$info[$i]["l"][0][0]?>" size=1 maxlength=1 /> 
				</td> -->
				<td><input name="comments" value="<?=substr($info[$i]["l"][0],3)?>" maxlength="50" size="20" /></td>
				<td><button type="submit">Speichern</button></td>
				</form>
				</tr>
			<?
			}
		}

		?>
		
                </table><br/>
<!--                Status:<br/>
                0 - unbearbeitet<br/>
                1 - positiv erledigt<br/>
                2 - negativ erledigt (bitte <b>kommentieren!</b>)<br/>
                <br/> -->
                <?
        } else {
                ?>Keine Notebooks. <span class="error">ERROR!?</span><br/><?
        }
}

ldap_close($ldap);
sas_end();
