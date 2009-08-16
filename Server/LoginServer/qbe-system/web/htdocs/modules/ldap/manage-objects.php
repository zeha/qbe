<?php
require"../../sas.inc.php";
sas_start("Objekte verwalten","../../","/modules/core",1);
sas_showmenu();

sas_varImport('prefill');

	function usersearchbox()
	{	global $prefill,$user;
	
		qbe_web_maketable(true); ?>
		
		<tr><th colspan=2>Benutzer anzeigen</th></tr>
		<tr>
			<form action="display-user.php" method=get>
			<td align=right width=60><label>ID:</label></td>
			<td>
				<input type="text" name="uid" id="uid"> <? qbe_web_makelookupbutton();?>
				<button type=submit>Anzeigen</button>
			</td>
			</form>
		</tr>
		<?
		if (sas_ldap_isgroupmember("useradm",$user)) {?>
		<tr><th colspan=2>Benutzer importieren (<?=sas_makehelplink('import')?>)</th></tr>
		<tr>	<td></td>
			<td><a href="../../modules/filexs/?hideactions=1&actionlink=<?=urlencode('/modules/ldap/import?type=')?>">Datei ausw&auml;hlen</a></td>
		</tr>

		<tr><th colspan=2>Passw&ouml;rter importieren</th></tr>
		<tr>	<td></td>
			<td><a href="../../modules/filexs/?hideactions=1&actionlink=<?=urlencode('/modules/ldap/import_passwords?type=')?>">Datei ausw&auml;hlen</a></td>
		</tr>
		<? }
		if (sas_check_group("useradm") || sas_check_group("userinetchange")) {?>
		<tr><th colspan=2>Nachricht senden</th></tr>
		<tr>	
			<form action="../../modules/core/sendmsg" method=post>
			<td align=right width=60><label>ID:</label></td>
			<td>
				<input type="text" name="uid" id="uid"> <? qbe_web_makelookupbutton();?>
				<button type=submit>Weiter</button>
			</td>
			</form>
		<? } ?>
		</table>
		<br>
		<a href="report/list?filter=<?=urlencode("(&(ou=*)(objectClass=organizationalUnit))")?>">Klassen auflisten</a><br>
		<br>
	<?}
	
	function classeditbox()
	{
		global $prefill,$qbe_report_templates;
		
		qbe_web_maketable(true);
	?>
		<tr>
		<th colspan=2>Klasse editieren</th>
		</tr>
		<tr>
		<form action="edit-class.php" method=get>
		<td align=right width=60>Klasse:</td>
		<td>
	<input name="klasse" id="klasse" value=""/>
	<? qbe_web_makelookupbutton('klasse','class'); ?>
	<button type=submit>Anzeigen</button> </td>
		</form>
		</tr>
		</table>
		<br>
	<?php
	}

	function groupeditbox()
	{
		global $prefill,$qbe_report_templates;

		qbe_web_maketable(true);
	?>
		
		<form action="edit-groups.php" method=get>

		<tr>	<th colspan=2>Gruppen bearbeiten</th></tr>
		<tr>	<td align=right width=60>Klasse:</td>
			<td><input name="class" id="grp_class" value=""/>
			<? qbe_web_makelookupbutton('grp_class','class'); ?>
			<button type=submit>Anzeigen</td>
			</td>
		</tr>

		</form>
		
		</table>
		<br>

	<?php

	}
	
	if (sas_ldap_isgroupmember("useradm",$user))
	{
		usersearchbox();
		classeditbox();
	} elseif (sas_ldap_isgroupmember("passchange",$user))
	{
		usersearchbox();
	}
	
	if (sas_ldap_isgroupmember("groupadm",$user))
	{
		groupeditbox();
	}

sas_end();
