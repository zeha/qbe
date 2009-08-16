<?
//
// SAS 0.9.4 - 10. Feb. 2004 - ch
// fuck sk00l!
//

include "../../sas.inc.php";
sas_start("PC neu zuordnen","../../","/admin",1);
sas_showmenu();

sas_varimport('name');
sas_varimport('uid');

if ($name == "")
{
	sas_pcode('error','Falsche Parameter');
	sas_end();
	exit;
}

if ($uid != '') { if (sas_ldap_getdn($uid) == '') { $uid = ''; } }

if ($uid == '')
{

	?>
	<form method=post>
	<input type=hidden name="name" value="<?=$name?>">
	Benutzer: <input type=text name="uid" id="uid" value=""> <? qbe_web_makelookupbutton('uid','user'); ?>
	<button type=submit>&Auml;ndern</button>
	</form>
	<?

} else {
	

	?><b>Using Client: <?=$name?></b><br><?

	$error = false;

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	if ($b)
	{
		$sr = ldap_search($ds,$sas_ldap_base,"(uid=".$name.")");
		$res = ldap_get_entries($ds,$sr);
		if ($res["count"] > 0)
		{
			$owner = $res[0]["owner"][0];
			$newowner = sas_ldap_getdn($uid);
			
			echo '<b>Previous Owner: '.sas_ldap_getusername($owner).'</b><br>';
			echo '<b>New Owner: '.sas_ldap_getusername($newowner).' ('.$uid.')</b><br>';
			if ($owner == $user)
			{
			//	$error = !ldap_delete($ds,$res[0]["dn"]);
			//	$errstr = "PC konnte nicht gel&ouml;scht werden.";

				$newinfo = array();
				$newinfo["owner"] = $newowner;
				$error = !ldap_modify($ds,$res[0]["dn"],$newinfo);
				$errstr = "Objekt konnte nicht ge&auml;ndert werden.";
			} else {
				$error = true;
				$errstr = "Sie sind nicht der Eigent&uuml;mer.";
			}
			
		} else { $error = true; $errstr = "Kann PC-Objekt nicht finden!"; }
		ldap_close($ds);
		
		if (!$error)
		{
			qbe_log_text("qbe-appmodule-hosts-userworkstation",LOG_NOTICE,"Reowned workstation: $name for $newowner from $userid");
		}
	} else {
		$error = true; $errstr = "Logon Error";
	}
	

	if ($error)
	{
  		?><br/>
		<?
			sas_pcode('error',($errstr == '') ? 'Ein Fehler ist aufgetreten.' : $errstr);
  	} else {
		?><br/>
		<b>OK</b>, ge&auml;ndert.
		<?
	}

}

sas_end();
