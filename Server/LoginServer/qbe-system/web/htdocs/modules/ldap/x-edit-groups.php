<?
include "../../sas.inc.php";
sas_start("Gruppenverwaltung","../../../","/modules/ldap",1);
sas_showmenu();
qbe_restrict_access("groupadm");

$showuid = 0;
if (isset($_POST['group'])) { $group = $_POST['group']; } else { $group = ""; }
if (isset($_GET['class'])) { $class = $_GET['class']; } else { $class = ""; }
if (isset($_GET['showuid'])) { $showuid = intval($_GET['showuid']); } else { $showuid = 0; }

if ($class == "")
{
	?><form action="<?=$PHP_SELF?>" method=get>Klasse: <input name="class"><input type=submit value="show"></form><br />
	<?
	sas_end();
	exit;
}


function print_list()
{global $class, $sas_ldap_server, $sas_ldap_adminuser, $sas_ldap_adminpass, $PHP_SELF, $showuid, $sas_ldap_base;


        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

	$classresult = ldap_search( $ldap , "ou=Students,ou=People,".$sas_ldap_base , "(ou=$class)");
	$classinfo = ldap_get_entries($ldap, $classresult);
	if ($classinfo["count"] == 0) { echo "<th><td colspan=\"5\">Klasse nicht gefunden!<br/></td></th>\n"; }
	ksort($classinfo,SORT_STRING);
	reset($classinfo);

        $groupresult = ldap_search( $ldap , "ou=Classes,ou=People,".$sas_ldap_base , "(ou=$class)");
        $groupinfo = ldap_get_entries($ldap, $groupresult);
	ksort($groupinfo,SORT_STRING);
	reset($groupinfo);

	$mygroups = array();
	for ($i = 1; $i < $groupinfo["count"]; $i++)
	{
		$mygroups[$i] = strtoupper($groupinfo[$i]["cn"][0]);
	}
	$mygroupcount = $i-1;
	
	sort($mygroups);

	for ($i = 1; $i < $groupinfo["count"]; $i++)
	{
		for ($group = 0; $group < $mygroupcount; $group++)
		{
			if ($mygroups[$group] == strtoupper($groupinfo[$i]["cn"][0]))
			{
				$mymembers[$group] = array();
				$m = 0;
				foreach ($groupinfo[$i]["member"] as $member)
				{
					if (substr($member,0,3) == "uid")
					{
						$m++;
						$mymembers[$group][$m] = $member;
					}
				}
				break;
			}
		}
	}

	qbe_web_maketable(true);

	?>
	<form action="addgroup.php">
	<input type="hidden" name="class" value="<?=$class?>">

		<tr>
		<th colspan=2>
			Neue Gruppe: 
		</th>
		</tr>
		<tr>
		<td align=right width=60><label>Gruppe:</label></td>
		<td><input type="text" name="group">
		<button type="submit">Anlegen</button></td>
		</tr>
		
		</table>
		<br>
	<?

	qbe_web_maketable(true,'width=400');
	?>
	<tr>
	<th>Gruppen</th>
	<?
	
	for ($group=0; $group<$mygroupcount; $group++)
        {	$gn = substr($mygroups[$group],4);	?>

		<th align=center><?=$gn?><br />
			<a href="del-group?class=<?=$class?>&group=<?=$gn?>">Del</a>	
		</th>
	<?}?>

	</tr>

	</form>

	<form action="<?=$PHP_SELF?>?class=<?=$class?>" method=post>

	<?
	for ($xclass=0; $xclass<$classinfo["count"]; $xclass++)
	{
		qbe_web_maketr();
	?>

		<td><a href="display-user?uid=<?=$classinfo[$xclass]["uid"][0]?>"><?=$classinfo[$xclass]["cn"][0]?></a></td>
		<?

		        for ($group=0; $group<$mygroupcount; $group++)
        		{ ?>

		<td align=center>
		<input type="checkbox" value=1 class="checkbox" <?

		foreach ($mymembers[$group] as $member)
		{	// do a case-insensitive (note the "case") against the dn and the member
			$member = str_replace(" ","",strtolower($member));
			$dn = str_replace(" ","",strtolower($classinfo[$xclass]["dn"]));
			if (!strcmp($member,$dn))
			{	echo "CHECKED ";
				break;
			}
		}?> name="group[<?=$mygroups[$group]?>][<?=$classinfo[$xclass]["uid"][0]?>]">
		</td>

<?	}	?>

	</tr>

<?	}	?>
	<tr>
		<td colspan="<?=$mygroupcount+1?>" align=right>
			<button type="submit">Speichern</button>

		</td>
	</tr>
	</table>

<?
	for ($group=0; $group<$mygroupcount; $group++)
	{
		?>
	<input type="hidden" value=1 name="group[<?=$mygroups[$group]?>][none]">
		<?
	}
?>
</form>
<br>
<?}
if ($group == "") {
	print_list();
} else {

	$error = false;

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	if ($b)
	{
		$group = $class . '-' . $group;
		// can login, create user
		$info["objectClass"][0]="QbeGroup";
		$info["objectClass"][1]="groupOfNames";
	#	$info["member"] = "ou=".$class.",ou=Classes,ou=people,o=htlwrn,c=at";
		$info["ou"] = $class;
		$info["inetStatus"] = 1;

		foreach($HTTP_POST_VARS['group'] as $g_group=>$g_users)
		{
			$info["cn"] = $g_group;
			$dn = "cn=" . $g_group . ",ou=" . $class . ",ou=Classes,ou=People,o=htlwrn,c=at";
			unset($info["member"]);
			$info["member"][0] = "ou=".$class.",ou=Classes,ou=people,o=htlwrn,c=at";
		#	echo "$g_group - ou=".$class.",ou=Classes,ou=people,o=htlwrn,c=at<br>";
			$i = 0;
			foreach($g_users as $g_user=>$g_value)
			{
				if ($g_user != "none")
				{
				$i++;
				$g_abt = $g_user[0];
				$info["member"][$i] = "uid=".$g_user.",ou=".$g_abt.",ou=Students,ou=People,o=htlwrn,c=at";
#	echo "uid=".$g_user.",ou=".$g_abt.",ou=Students,ou=People,o=htlwrn,c=at<br>";
				}
//echo "saving ".$g_group." - ".$g_user."<br>";
			}
			
			$r=ldap_modify($ds, $dn, $info);
			if (! $r) {
				$error = true;
				echo ldap_error($ds) . "<br>";
			}
		}

		
		// add data to directory
//		echo "cn=" . $group . ", ou=" . $class . ", ou=Classes, ou=People, o=htlwrn, c=at<br>";
		ldap_close($ds);
	} else {	$error = true;	}

	if ($error)
	{
  		?>
 		<span class="error">Ein Fehler ist aufgetreten.<br></span>
		<?
  	} else {
		?>
		<b>Gespeichert.</b><br/>
		<?
		print_list();
	}

}

sas_end();
?>
