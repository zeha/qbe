<?
include "../../sas.inc.php";
sas_start("Gruppe Anlegen","../../../","/admin/admin",1);
sas_showmenu();
qbe_restrict_access("groupadm");

sas_varimport('group');
sas_varimport('class');

	$class = strtoupper($class);
	$group = strtoupper($group);

if ( ($group == "") || ($class == "") )
{
	sas_pcode('error','Klassen-/Gruppenname ung&uuml;ltig.');
	?>

	<form action="<?=$PHP_SELF?>" method=post>
		<table>
			<tr><td>Klasse:</td> <td><input type="text" name="class" value="<?=$class?>"><br></td></tr>
			<tr><td>Gruppe:</td> <td><input type="text" name="group" value="<?=$group?>"><br></td></tr>
			<tr><td></td><td><button type="submit">Anlegen</button></td></tr>
		</table>
	</form>

	<br>
	<?

} else {
	$error = false;

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	if ($b)
	{
		$group = $class . '-' . $group;
		// can login, create user
		$info["objectClass"]="groupOfNames";
		$info["ou"] = $class;
		$info["cn"] = $group;
		$info["member"] = "ou=".$class.",ou=Classes,ou=people,o=htlwrn,c=at";

		// add data to directory
		echo "cn=" . $group . ", ou=" . $class . ", ou=Classes, ou=People, o=htlwrn, c=at<br>";
		$r=ldap_add($ds, "cn=" . $group . ", ou=" . $class . ", ou=Classes, ou=People, o=htlwrn, c=at", $info);
		if (! $r) {
			$error = true;
			$errstr = ldap_error($ds);
		}
		ldap_close($ds);
	} else {	$error = true;	}

	if ($error)
	{
		sas_pcode('error','LDAP: '.$errstr);
  	} else {
		sas_pcode('success','Gruppe angelegt.');
	}

}

sas_end();
?>
