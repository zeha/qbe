<?
if (isset($_GET['klasse'])) { $klasse = $_GET['klasse']; } else { $klasse = ""; }
include "../../sas.inc.php";

 sas_start("Klasse editieren","../../","/admin/tools",2);
 sas_showmenu();

if ($klasse != "")
{
        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

        $result = ldap_search( $ldap , "ou=People,".$sas_ldap_base , 
		"(& (ou=$klasse) (uid=*))", 
		array("dn","ou","uid","cn","inetStatus","ipHostNumber"),0,500);
	
	$info = ldap_get_entries($ldap, $result);
	qbe_web_maketable(true);
	?>
	<tr>
		<th align=right>Klasse</th><th>Name</th><th>ID</th>
		<th>inet</th>
		<th>ip</th>
		<th></th>
	</tr>
	<?
	for ($i=0; $i<$info["count"]; $i++)
	{
		if (isset($info[$i]["uid"])) {
		qbe_web_maketr();
		?>
	<form action="save-user" method=post>
	<td align=right>
	<input type=hidden name="userdn" value="<?=$info[$i]["dn"]?>">
	<input name=newclass size=3 value="<?=$info[$i]["ou"][0]?>"></td>
	<td align=right><?=$info[$i]["cn"][0]?></td>
	<td>
	<a href="<?=$qbe_report_templates['user-by-uid'].$info[$i]["uid"][0]?>">
	<?=$info[$i]["uid"][0]?></a>
	</td>
	<td><input name="inetstatus" size=2 value="<?=$info[$i]["inetstatus"][0]?>"></td>
	<td><input name="ip" size=8 value="<?=$info[$i]["iphostnumber"][0]?>"></td>
	<td><button type=submit>Speichern</button></td>
	</form></tr>
	<?}
	}
	?>
<script>
	function confirmit()
	{
		check = confirm('Wollen Sie wirklich die ganze Klasse löschen?');
		return check;
	}
</script>
	<tr>
	<td colspan=5 align=right>
		<a href="move-class?class=<?=$klasse?>">Umbenennen</a>
	</td>
<form method=post action="del-class.php" onSubmit="javascript:return confirmit();">
<input type=hidden name=class value="<?=$klasse?>">
	<td><button type=submit>L&ouml;schen</button></td>
	
	</tr>
	</table>
	<?

	ldap_close($ldap);
} else {
	?><br/>
	<span class="error">Bitte ben&uuml;tzen Sie das Men&uuml; um diese Funktion aufzurufen.</span><br/>
	<?
}
sas_end();
?>
