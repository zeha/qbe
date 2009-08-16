<?php
	require "../../sas.inc.php";
	error_reporting(0);

	$user = $_POST['user'];
	$pass = $_POST['pass'];

	session_set_cookie_params(0);
	session_start();

	// get dn of user
	$user = sas_ldap_getdn($user);

	if (isset($_GET['go'])) $redir = $_GET['go'];   else $redir = "";
	if (isset($_POST['go'])) $redir = $_POST['go'];
	if ($redir == "") $redir = "/";

	if ($user == "")
		$pass = "";

	$isok = FALSE;

function checkactivation()
{
  global $sas_ldap_server, $sas_ldap_adminuser, $sas_ldap_adminpass, $user;

  $ds = ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	$list = ldap_read ( $ds, $user , "objectClass=*" );
        $entry = ldap_first_entry($ds, $list);
	$attrs = ldap_get_attributes($ds, $entry);
	$value = @$attrs["inetStatus"][0];
	ldap_close($ds);
	if ($value > 999)
		return TRUE;
		else
		return FALSE;
  }
}
function checkStatus()
{
  global $sas_ldap_server, $user, $pass, $sas_ldap_adminuser,$sas_ldap_adminpass;

  $ds = ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = ldap_bind($ds,$user,$pass);
        $list = ldap_read ( $ds, $user , "objectClass=posixAccount" );
	$attrs = ldap_get_entries($ds,$list);
	ldap_close($ds);
	$inet = @$attrs[0]["inetstatus"][0];
	if ($inet == 3)
	{
		return FALSE;
	} else {
		return TRUE;
	}
  } else {
  	return FALSE;
  }
  
}

if ($user == "" or $pass == "")
{
	$user = ""; $pass = "";
	$isok = FALSE;
} else {
  
	$isok = FALSE;
	$needpwupdate = FALSE;
	if (sas_ldap_checkpassword($user,$pass))
	{
		$isok = TRUE;
		if (checkStatus() == FALSE)
		{
			$isok = FALSE;
		}
	}

  if ($isok)
  {
	// looks good
	$valid = 1;
	$_SESSION['valid'] = 1;
	$_SESSION['user'] = $user;
	$_SESSION['pass'] = $pass;
	$_SESSION['uid'] = sas_ldap_getuid($user);
	$_SESSION['ou'] = '';
	$_SESSION['abteilung'] = '';
	
	$ds = @ldap_connect($sas_ldap_server);
	if ($ds)
	{
		$r = ldap_bind($ds,$sas_ldap_machineuser,$sas_ldap_machinepass);
		$list = ldap_read ( $ds, $user, "objectClass=*" );
		$entry = ldap_first_entry($ds, $list);
		$attrs = ldap_get_attributes($ds, $entry);
		$_SESSION['ou'] = $attrs['ou'][0];
		if (strstr($user,'ou=People') != '')
		{
			$_SESSION['abteilung'] = strtoupper(substr(strstr($user,'ou='),3,1));
		}
		ldap_close($ds);
	}

	$uid = sas_ldap_getuid($user);
	$ou = $_SESSION['ou'];
	$abteilung = $_SESSION['abteilung'];

	session_register($valid,$user,$pass,$uid,$ou,$abteilung);
	session_write_close();

	// check for activated account
	if ($needpwupdate == FALSE) { $needpwupdate = checkactivation(); }
	
	if ($needpwupdate)
	{
		$redir = "/admin/activation$sas_phpext?go=" . rawurlencode($redir);
	}

	if (strstr($redir,"?") == "")   $redir .= "?" . md5(time()); else $redir .= "&x=" . md5(time());
	
  }

  if ($isok)
  {
	header("Location: $redir");
  }
 }

if (!$isok)
{
  ?>
<html>
<head>
	<title>Qbe Login</title>
	<link rel=stylesheet href="../../graphics/style-login.css">
</head>
<body>
<br/><br/><br/>
<center>
<div class="content"> 
 <br/>
<table>
<tr>
 <td>
	<a href="/"><img src="../../graphics/qbe.sas.topright.png" border="0"></a>
 </td>
 <td class="textbig">Fehler</b></td>
</tr>
<tr>
<td>
</td>
 <td class="textinfo">
 	Der Anmeldevorgang schlug aufgrund von falschen Anmeldedaten fehl.<br>
	Bitte versuchen Sie es <a href="../core/login?go=<? echo rawurlencode($redir); ?>">erneut.</a>
 </td>
</table>


 <br/>

</div>
</center>
<br/>
<br/>
<br/>
</body></html>
<?
}
?>
