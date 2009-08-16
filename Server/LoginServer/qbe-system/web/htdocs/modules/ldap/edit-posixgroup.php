<?php
require '../../sas.inc.php';
sas_start("posixGroup &auml;ndern","../../","/modules/core",2);
sas_showmenu();

	sas_varImport('dn');
	sas_varimport('users');

	echo "<form method=post><select name=dn size=1>\n";
	$ds = ldap_connect($sas_ldap_server);
	ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);

		$r = ldap_list($ds,'ou=group,ou=Administration,'.$sas_ldap_base,'(objectClass=*)');
		$e = ldap_get_entries($ds,$r);
		foreach($e as $group)
		{	if($group['cn'][0]!='')
			echo '<option value="'.$group['cn'][0].'" '.(($dn==$group['cn'][0]) ? 'SELECTED="selected"' : '').'>'.$group['cn'][0].'</option>';
		}

	echo "\n</select> <button type=submit>ok</button></form><br>\n";
	
	if ($dn != '')
	{

		if ($users == array()) { $users == ''; }

		if (is_array($users))
		{
			if (isset($users["new"]))
			{
				// we have to add a user
			}
			if (isset($users["delete"]))
			{
			}
		}

		$fdn = 'cn='.$dn.',ou=group,ou=Administration,'.$sas_ldap_base;
		$r = @ldap_read($ds,$fdn,'(objectClass=*)',array('member'));
		if ($r != FALSE)
		{
			$e = ldap_get_entries($ds,$r);

			echo '<b>Current group members:</b><br><form method=post>';
			if ($e['count'] > 0)
			{
		
				foreach($e[0]["member"] as $member)
				{	if (strlen($member)>4)
					printf("&nbsp; <input type=checkbox name=\"users[delete][".$member."]\" value=1>".$member."<br>\n");
				}
			} else { echo 'none'; }

			?>
			<input type=hidden name="dn" value="<?=$dn?>">
			&nbsp; <input type=text name="users[new]" size=100><br>
			<br>
			<button type=submit>markierte loeschen und oder neuen hinzufuegen</button>
			<?php
		}
	}

	ldap_close($ds);

sas_end();
