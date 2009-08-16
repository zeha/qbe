<?
include "../../sas.inc.php";
sas_start("Klasse umbenennen","../../","/modules/ldap",2);
sas_showmenu();

sas_varimport("class");
sas_varimport("newclass");
sas_varimport("confirm");

	error_reporting(15);

if ($confirm == 1)
{
?>Verschiebe Klasse <em>"<?=$class?>"</em> auf <em>"<?=$newclass?>"</em>.<br/>
<br/>
<?php
	// Zap out results from previous queries
	qbe_ldap_clearcache();

	// Check the old class name
	if (	($class == "") ||
		(($dn_class = qbe_ldap_getobjectdn("(&(ou=".$class.")(objectClass=qbeOrganizationalUnit))")) == "")
	) {
		sas_pcode('error','Angegebene Klasse ung&uuml;ltig/nicht gefunden.');
		sas_end();
	} else {
		printf("Class found... ok<br>\n");
	}

	// Check the new class name
	if (	($newclass == "") ||
		(($dn_newclass = qbe_ldap_getobjectdn("(&(ou=".$newclass.")(objectClass=qbeOrganizationalUnit))")) != "")
	) {
		sas_pcode('error','Angegebene Klasse ung&uuml;ltig bzw. existiert bereits.');
		sas_end();
	}

	// 1, 2, 3, go...
	$ds = ldap_connect($sas_ldap_server);
	if (!$ds) { sas_pcode('error','Cant contact LDAP server'); sas_end(); }
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	echo "ldap set protocol v3...".ldap_error($ds)."<br>\n";
	if (ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass))
	{
		printf("LDAP login... ".ldap_error($ds)."<br>\n");

		printf("Old class object: ".$dn_class."<br>\n");
		printf("New class object: ".$dn_newclass." (empty is good!)<br>\n");

		$dn_newclass = "ou=".$newclass.",ou=Classes,ou=People,".$sas_ldap_base;
		printf("New class object name: ".$dn_newclass."<br>\n");

		// Find group objects
		$rs = ldap_search($ds,$dn_class,'(&(cn='.$class.'-*)(objectClass=qbeGroup))',array("ou","dn","member","cn"));
		echo ldap_error($ds);
		if ($rs != FALSE)
		{
			$entries = ldap_get_entries($ds,$rs);
			sas_pcode('info',"Found ".$entries['count']." group objects to rename.");
			
			$i = 1;
			$startgroupname = strlen($class)+1;
			foreach($entries as $object)
			{
				if (isset($object["dn"]))
				{
					printf("found group ".$i++.": ".$object['cn'][0]." -&gt; \n");
/*					$groupname = substr($object['cn'][0],$startgroupname);
					$newcn = $newclass . '-' . $groupname;
					printf($newcn."... \n");
					
					$newmembers = array($dn_newclass);
					foreach($object['member'] as $key => $member)
					{
						if (is_numeric($key) && strcasecmp($member,$dn_class))
							array_push($newmembers,$member);
					}

					ldap_mod_replace($ds,$object["dn"],array('ou' => $newclass,'member' => $newmembers));
					echo ldap_error($ds) . " ... ";

					ldap_rename($ds,$object["dn"],'cn='.$newcn.','.$dn_newclass,TRUE);
					echo ldap_error($ds);
*/
					// hm didn't figure out how to get that working correctly
					// it may be better to delete the objects though, anyway.
					ldap_delete($ds,$object['dn']);
					echo ldap_error($ds);
					echo "<br>\n";
				}
			}
		}


		// Find user objects + update ou attribute
		$rs = ldap_search($ds,"ou=Students,ou=People,".$sas_ldap_base,"(&(objectClass=inetOrgPerson)(ou=".$class."))",array("ou","dn"));
		if ($rs != FALSE)
		{
			$entries = ldap_get_entries($ds,$rs);
			if ($entries['count'] == 0)
			{
				sas_pcode('attention','Found 0 user objects to rename.');
			} else {
				sas_pcode('info',"Found ".$entries['count']." user objects to rename.");

				$i = 1;
				foreach($entries as $object)
				{
					if (isset($object["dn"]))
					{
						printf("Moving user ".$i++.": ".$object["dn"].": \n");
						ldap_mod_replace($ds,$object["dn"],array('ou' => $newclass));
						printf(ldap_error($ds) . "<br>\n");
					}
				}
			}
			
		} else {
			sas_pcode('attention','Found 0 user objects to rename!');
		}

		// Rename the class object...
		$newrdn = "ou=".$newclass;
		$newbase = "ou=Classes,ou=People,".$sas_ldap_base;
		printf("new rdn: ".$newrdn."<br>\n");
		printf("new base: ".$newbase."<br>\n");
		ldap_rename($ds,$dn_class,$newrdn,$newbase,FALSE);
		$err = ldap_error($ds);
		if ($err == 'Success')
		{
			sas_pcode('success','Class object renamed successfully.');
		} else {
			sas_pcode('attention','Class object rename: ('.ldap_errno($ds).') '.$err);
		}
		
	
		ldap_close($ds);
	} else
		sas_pcode('error','Cant login to LDAP server');

} else {
	?>
	<form method=post>
	
		<? sas_pcode('attention','Dieser Vorgang wird Gruppen-Objetkte l&ouml;schen!');?>
		<br />
		<?
		qbe_web_maketable('width=400');
		qbe_web_maketr();
		?>
		<td>Alter Klassenname:</td>
		<td><input type=text name="class" value="<?=$class?>" /></td>
		</tr>
		<? qbe_web_maketr(); ?>
		<td>Neuer Klassenname:</td>
		<td><input type=text name="newclass" value="<?=$newclass?>" /></td>
		</tr>
		<? qbe_web_maketr(); ?>
		<td></td><td><br/></td>
		</tr>
		<? qbe_web_maketr(); ?>
		<td>Best&auml;tigung:</td>
		<td><input type=checkbox name="confirm" value="1" /></td>
		</tr>
		<? qbe_web_maketr(); ?>
		<td><br /></td>
		<td><button type=submit>Umbenennen</button></td>
		</tr>
		</table>
	
	</form>
	<?php
}

	// Done.
	sas_end();

