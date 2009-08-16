<?
include "../../sas.inc.php";
sas_start("Benutzerdaten speichern","../../","/admin/admin",1);
sas_showmenu();

qbe_restrict_access('useradm|passchange|userinetchange');

echo 'Rechte: ';

$inetstatus = -1; $ip = ""; $mac = ""; $newclass = "";
$newpass  = ""; $newpass2 = ""; $userdn = ""; $inetstatus = ""; $traffic = "";

$action = 'save';
if (sas_ldap_isgroupmember('useradm',$user))
{
 if (isset($_POST['save'])) { $action = 'save'; }
 if (isset($_POST['delete'])) { $action = 'delete'; }

 if (isset($_POST['ip'])) { $ip = $_POST['ip']; }
 if (isset($_POST['mac'])) { $mac = $_POST['mac']; }
 if (isset($_POST['newclass'])) { $newclass = $_POST['newclass']; }
 
 echo 'Objekt ';
} 
if ( sas_check_group('useradm') || sas_check_group('userinetchange') )
{
 echo 'InetStatus ';
 if (isset($_POST['inetstatus'])) { $inetstatus = $_POST['inetstatus']; }
 if (isset($_POST['traffic'])) { $traffic = $_POST['traffic']; }
}
if ( sas_check_group('useradm') || sas_check_group('passchange') )
{
 echo 'Passwort';
 if (isset($_POST['newpass']))  { $newpass  = $_POST['newpass'];  } else {$newpass  = "";}
 if (isset($_POST['newpass2'])) { $newpass2 = $_POST['newpass2']; } else {$newpass2 = "";}
}

if (isset($_POST['userdn'])) { $userdn = $_POST['userdn']; } else {$userdn = ""; $inetstatus = "-1"; }
if ($inetstatus <> "") { $inetstatus = intval($inetstatus); }
if ($inetstatus == "") { $inetstatus = -1; }
echo '<br>';

if ($userdn == "")
{?>
	<span class="error">Nothing to do.</span><br>
<?} else {
	$error = false;

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	if ($b)
	{
		if ($action == 'save')
		{
			$info = array();
			if ($inetstatus != -1) { $info["inetStatus"][0]=$inetstatus; }
			if ($newclass != "") {$info["ou"]=$newclass; }
			
			if ($ip != "") { $info["ipHostNumber"]=$ip; }
			if ($mac != "") { $info["macAddress"]=$mac; }
			if ($traffic != "") { $info['traffic']=$traffic; }
			if ($newpass != $newpass2) { $newpass = ""; $error = true;
			?><span class="error">Passw&ouml;rter sind nicht gleich!<br></span>
			<?}
			if ($newpass != "") { sas_changepassword($sas_ldap_adminuser,$sas_ldap_adminpass,$userdn,$newpass);  }
			$r = FALSE;

			if (!$error)
				$r=ldap_modify($ds, $userdn, $info);

		}
		if ($action == 'delete')
		{
			$r=ldap_delete($ds, $userdn);
		}	

		if (! $r)
			$error = true;
	} else {	$error = true;	}

	if ($error)
	{
  		?>
 		<span class="error">Ein Fehler ist aufgetreten.<br></span>
		<?
  	} else {
		?>
		OK. (<a href="<?=$_SERVER['HTTP_REFERER']?>">Zur&uuml;ck</a>)
		<?
	}

	ldap_close($ds);

}

sas_end();
?>
