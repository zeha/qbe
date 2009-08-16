<?
//
//

include "../../sas.inc.php";
sas_start("Computerverwaltung","../../","/admin",1);
sas_showmenu();

qbe_restrict_access("teachers");

$whall = false;
if (sas_ldap_isgroupmember("halladm",$user))
{
	$whall = true;
}

?>
<script>
	function check(id)
	{
		var box;
		box = document.getElementById("check"+id);
		box.checked = true;
	}
</script>

<form method=post action=act>
Sie haben folgende PCs registriert:<br>
<br>
<? qbe_web_maketable(true,'width=450'); ?>
<tr><th></th><th>Name</th><th>IP</th><th>MAC</th><th>NID</th><th>Policy</th><th>Eigent&uuml;mer</th></tr>
<?

$ds = ldap_connect($sas_ldap_server);
if ($ds)
{	ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	$sr = ldap_search($ds,"ou=hosts,ou=Administration,".$sas_ldap_base,
			"(owner=".$user.")");
	$info = ldap_get_entries($ds,$sr);
	if ($info["count"])
	{	
		for ($id = 0; $id < $info["count"]; $id++)
		{
			qbe_web_maketr();
		?>
			<td><input type=checkbox class=checkbox name="pcs[<?=$id?>]" id="check<?=$id?>" value="<?=$info[$id]["uid"][0]?>"></td>
			<td><?=$info[$id]["uid"][0]?></td>
			<td><?=$info[$id]["iphostnumber"][0]?></td>
			<td><?=$info[$id]["macaddress"][0]?></td>
			<td><?=$info[$id]["uidnumber"][0]?></td>
			<td><input type=text name="policy[<?=$info[$id]["uid"][0]?>]" value="<?=isset($info[$id]["qbepolicyname"]) ? ereg_replace("^cn=(.*),ou=(.*),ou=(.*)","\\1",$info[$id]["qbepolicyname"][0]) : "" ?>" onChange="check(<?=$id?>);"/></td>
			<td><input type=text name="owner[<?=$info[$id]["uid"][0]?>]" value="<?=sas_ldap_getuid($info[$id]["owner"][0])?>" onChange="check(<?=$id?>);" /></td>
			
			</tr>
		<?php
		}

		?>
		<tr><td colspan=7 align=right>
	<button type=submit name=action value="change">&Auml;ndern</button>
	<button type=submit name=action value="delete">L&ouml;schen</button>
		</td>
		</tr>
		<?php
	} else {
		?>
		<tr><td colspan=7 align=center><i>bisher keine</i></td></tr>
		<?php
	}
}

?>
</table>
</form>
<br />

<? function thisbox() { global $PHP_SELF, $sas_client_ip;
   qbe_web_maketable(true,'style="width: 26em;"'); ?>
<form action="add-client" method=post>
<tr><th colspan=2>Eigenen PC anlegen</th></tr>
<? qbe_web_maketr(); ?><td width=50>IP:</td> <td><input type="text" name="wip" value="<?=$sas_client_ip?>"><br></td></tr>
<? qbe_web_maketr(); ?><td>MAC:</td> <td><input type="text" name="wmac" value="<?=sas_web_getclientmac($sas_client_ip);?>"> <? sas_makehelplink('macaddr'); ?></td></tr>
<? qbe_web_maketr(); ?><td></td><td><button type="submit">Anlegen</button></td></tr>
</form>
</table><br/>
<? }

function hallbox() { global $PHP_SELF, $sas_client_ip; 
   qbe_web_maketable(true,'style="width: 26em;"'); ?>
<form action="add-client" method=post>
<tr><th colspan=2>Workstation anlegen</th></tr>
<? qbe_web_maketr(); ?><td width=50>Name:</td> <td><input type="text" name="wname"> (ohne $ !)<br></td></tr>
<? qbe_web_maketr(); ?><td width=50>IP:</td> <td><input type="text" name="wip" value="<?=$sas_client_ip?>"><br></td></tr>
<? qbe_web_maketr(); ?><td>MAC:</td> <td><input type="text" name="wmac" value="<?=sas_web_getclientmac($sas_client_ip);?>"> <? sas_makehelplink('macaddr'); ?></td></tr>
<? qbe_web_maketr(); ?><td></td><td><button type="submit">Anlegen</button></td></tr>
</form>
</table><br/>
<? }

	thisbox();
	
	if (sas_ldap_isgroupmember("halladm",$user))
	{
		hallbox();
	}

	?>
	<b>&nbsp; Die obigen Standard-Werte gelten f&uuml;r den PC auf dem Sie die Eintragung vornehmen!</b><br/>
	<?

	sas_end();
