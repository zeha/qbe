<?
//
// SAS 0.9.4 - 10. Feb. 2004 - ch
// fuck sk00l!
//

include "../../sas.inc.php";
sas_start("PC L&ouml;schen","../../","/admin",1);
sas_showmenu();

sas_varimport('name');

if ($name == "")
{
?>

YOU D1D S0M3TH1NG WR0NG!<br>
:wq<br>

<?
}
	else
{

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
			echo '<b>Owner: '.sas_ldap_getusername($owner).'</b><br>';
			echo '<b>LDAP DN: '.$res[0]["dn"].'</b><br>';
			if ($owner == $user)
			{
				$error = !ldap_delete($ds,$res[0]["dn"]);
				$errstr = "PC konnte nicht gel&ouml;scht werden.";
			} else {
				$error = true;
				$errstr = "Sie sind nicht der Eigent&uuml;mer.";
			}
			
		} else { $error = true; $errstr = "Kann PC-Objekt nicht finden!"; }
		ldap_close($ds);
		
		if (!$error)
		{
			qbe_log_text("qbe-appmodule-hosts-userworkstation",LOG_NOTICE,"Deleted workstation: $name for $userid");
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
		<b>OK</b>, PC wurde gel&ouml;scht.</b>.
		<?
	}

}

sas_end();
