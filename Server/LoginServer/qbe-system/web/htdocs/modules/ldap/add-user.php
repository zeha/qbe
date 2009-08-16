<?
exit("Sorry! adduser.php is B R O K E N");
include "../../sas.inc.php";
sas_start("Benutzer anlegen","../../","/admin",2);
sas_showmenu();

$pass=$_SESSION['pass'];
$user=$_SESSION['user'];
$nuser=$_POST['nuser'];
$npass=$_POST['npass'];
$nname=$_POST['nname'];
$nrid=$_POST['nrid'];
$nuid=$_POST['nuid'];

if ($user == "")
	$pass = "";

if ($nuser == "")
	$npass = "";
if ($nname == "")
	$npass = "";
if ($nrid == "")
	$npass = "";
if ($npass == "")
	$nuser = "";

if ($npass == "")
{?>
<form action="<?=$PHP_SELF?>" method=post>
<table>
<tr><td>UIDNumber:</td> <td><input type="text" name="nuid"><br></td></tr>
<tr><td>Username:</td> <td><input type="text" name="nuser"><br></td></tr>
<tr><td>Passwort:</td> <td><input type="password" name="npass"><br></td></tr>
<tr><td>Name:</td> <td><input name="nname"><br></td></tr>
<tr><td>RID:</td> <td><input name="nrid"><br></td></tr>
<tr><td></td><td><input type="submit" value="Anlegen"></td></tr>
</table>
</form>
<br>
<?} else {
	$error = false;

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$user,$pass);
	if ($b)
	{
		// can login, create user
		$info["objectClass"]="person";
		$info["objectClass"]="InetOrgPerson";
		$info["objectClass"]="posixAccount";
		$info["objectClass"]="sambaAccount";
		$info["objectClass"]="shadowAccount";
		$info["objectClass"]="account";
		$info["userPassword"]=$npass;
		$info["loginShell"]="/bin/bash";
		$info["sn"]=$nname;
		$info["cn"]=$nname;
		$info["homeDirectory"]="/export/homes/" . $nuser;
		$info["uid"]=$nuser;
		$info["rid"]=$nrid;
		$info["ntPassword"]="";
		$info["lmPassword"]="";
		$info["shadowMax"]=99999;
		$info["shadowLastChange"]=1;
		$info["scriptPath"]="script.bat";
		$info["mail"]=$nuser . "@htlwrn.ac.at";
		$info["uidNumber"] = $nuid;
		$info["gidNumber"] = 201;
		$info["acctFlags"] = "[U          ]";

		// add data to directory
		$r=ldap_add($ds, "uid=" . $nuser . ", ou=People, o=htlwrn, c=at", $info);
		if (! $r)
			$error = true;
		ldap_close($ds);
	} else {	$error = true;	}

	if ($error)
	{
  		?>
 		<span class="error">Ein Fehler ist aufgetreten. Altes Passwort OK?<br></span>
		<?
  	} else {
		?>
		OK
		<?
	}

}

sas_end();
?>
