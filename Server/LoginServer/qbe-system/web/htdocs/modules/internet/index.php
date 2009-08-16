<?
// Seitenstart fuer SAS
include "../../sas.inc.php";
sas_start("Internetzugangskontrolle","../../","/modules/internet",1);
sas_showmenu();
qbe_restrict_access("inetlock");

echo 'Mehr ';
sas_makehelplink('inetlock');
echo ' dazu.<br><br>';

// HTTP GET Variablen in den Namespace importieren, list by default -> root
$list = (isset($_GET['list']) ? $_GET['list'] : "root");
$class = (isset($_GET['class']) ? $_GET['class'] : "");
$group = (isset($_GET['group']) ? $_GET['group'] : "");

// Zum LDAP verbinden und mit dem Machine user anmelden
$ds = ldap_connect($sas_ldap_server);
ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);

	function getLastSavedUser($class)
	{	global $sas_mysql_server,$sas_mysql_user,$sas_mysql_password,$sas_mysql_database;
		$ret = '';
		
		$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
		mysql_select_db($sas_mysql_database,$db);
		$sql = 'SELECT * FROM inetsavelog WHERE class="'.$class.'" AND classgroup="" AND newstate=0 ORDER BY saved DESC LIMIT 1';
		$res = mysql_query($sql);
		echo mysql_error();
		if ($res)
		{$row = mysql_fetch_array($res);
		 $ret = $row['userdn'];
		} else { $res = 'sys'; }
		mysql_close($db);
		return $ret;
	}

// Start des Formulars
echo "<form action=\"$PHP_SELF\" method=get>";
// Auswahl was der User sehen will
switch($list)
{	// den Klassenindex
 case "root":
	?>	Klassenliste:<br>
		<input type=hidden name=list value=class> 
	<?	// Alle Klassenobjekte auflisten
		$sr=ldap_list($ds,"ou=Classes,ou=People,o=htlwrn,c=at","(ou=*)",array("ou","inetstatus"),0,100,0);
		$entries = ldap_get_entries($ds,$sr);
		$count = $entries["count"];
		echo "<table><tr>";
		$states = array();
		for ($i = 0; $i < $count; $i++)
		{
			$classes[$i] = $entries[$i]["ou"][0];
			if (isset($entries[$i]["inetstatus"]))
			{
				$states[$classes[$i]] = $entries[$i]["inetstatus"][0];
			} else { $states[$classes[$i]] = "-1"; }
			
		}
		sort($classes);
		$classes[-1]="000";
		// Die Klassen sortiert ausgeben...
		for ($i = 0; $i < $count; $i++)
		{
			if ($classes[$i][0] != $classes[$i-1][0]) { echo "</tr><tr><td><br></td></tr><tr>"; }
			echo "<td>";
			//if ($classes[$i][1] != $classes[$i-1][1]) { echo "</td><td> "; }
			$displayname = $classes[$i];
			if (strlen($displayname) > 8) { $displayname = substr($displayname,0,4) . '-<br>' . substr($displayname,4); }
			$displayuser = '';
			if ($states[$classes[$i]] == 0) { $displayuser='<br>'.getLastSavedUser($classes[$i]).'&nbsp;'; }
			if (strlen($displayname) > 8) { $displayuser=''; }
			?>
	<td align=right>
	<a href="index.php?list=class&class=<?=$classes[$i]?>"><?=$displayname?></a>:<?=$displayuser?></td>
	<td style="line-height: 0;">
	<? /* aufgrund des alten status die ampel ausgeben */ ?>
	<a href="save.php?status=1&list=class&class=<?=$classes[$i]?>" title="Ganze <?=$classes[$i]?> sperren"><img src="/graphics/ampel_<?
	 if ( ($states[$classes[$i]] == 1) || ($states[$classes[$i]] == -2) )
	 { echo 'red'; }
	 if ($states[$classes[$i]] == -1 || $states[$classes[$i]] == 0)
	 { echo 'red_off'; }
	?>.png" border=0 style="border:1px solid black; padding: 0;"></a><br>
	
	<a href="save.php?status=0&list=class&class=<?=$classes[$i]?>" title="Ganze <?=$classes[$i]?> freigeben"><img src="/graphics/ampel_<?
	 if ( ($states[$classes[$i]] == 0) || ($states[$classes[$i]] == -2) )
	 { echo 'green'; }
	 if ($states[$classes[$i]] == -1 || $states[$classes[$i]] == 1)
	 { echo 'green_off'; }
	?>.png" border=0 style="border:1px solid black;"></a>
	</td><td>&nbsp;</td>
<?

		}
		echo "</table>";
	break;
 case "class":	// die Gruppenobjekte werden ausgegeben
	?>
	<table>
	<tr><td align=left> <? /* zuerst die Klassenampel */ ?>
		<a href="<?=$PHP_SELF?>?list=root"><b><?=$class?></b></a>:	</td><td align=left style="line-height: 0;"> 
		<a href="save.php?status=1&list=class&class=<?=$class?>"><img src="/graphics/ampel_red.png" border=0 style="border:1px solid black;i padding:0;"></a><br>
		<a href="save.php?status=0&list=class&class=<?=$class?>"><img src="/graphics/ampel_green.png" border=0 style="border:1px solid black; padding:0;"></a>
	</td>
	</tr>
	<?	/* alle Gruppen der Klasse $class auflisten */
		$sr=ldap_list($ds,"ou=$class,ou=Classes,ou=People,o=htlwrn,c=at","(objectClass=groupOfNames)",array("ou","gid","cn","inetstatus"));
		$entries = ldap_get_entries($ds,$sr);
		$count = $entries["count"];
		if ($count)
		{
		echo "<tr>";
		for ($i = 0; $i < $count; $i++)
		{	$groups[$i] = $entries[$i]["cn"][0];	
			$groups[$i] = substr(strchr($groups[$i],"-"),1);
			if (isset($entries[$i]["inetstatus"]))
			{
				$states[$groups[$i]] = $entries[$i]["inetstatus"][0];
			} else { $states[$groups[$i]] = "-1"; }
		}
		sort($groups);
		$groups[-1]="000";
		/* die Gruppenobjekte der Klasse sortiert und mit Statusampel ausgeben */
		for ($i = 0; $i < $count; $i++)
		{	if ($groups[$i][0] != $groups[$i-1][0]) { echo "</tr><tr><td><br></td></tr><tr>"; } 
			?>
	<td align=right><?=$groups[$i]?>:</td>
	<td style="line-height: 0;">
	<a href="save.php?status=1&list=group&class=<?=$class?>&group=<?=$groups[$i]?>"><img src="/graphics/ampel_<?
	 if ( ($states[$groups[$i]] == 1) || ($states[$groups[$i]] == -2) )
	 { echo 'red'; }
	 if ($states[$groups[$i]] == -1 || $states[$groups[$i]] == 0)
	 { echo 'red_off'; }
	 if ($states[$groups[$i]] == -2)
	 { echo 'red_off'; }
	?>.png" border=0 style="border:1px solid black; padding: 0;"></a><br>
	<a href="save.php?status=0&list=group&class=<?=$class?>&group=<?=$groups[$i]?>"><img src="/graphics/ampel_<?
	 if ($states[$groups[$i]] == 0)
	 { echo 'green'; }
	 if ($states[$groups[$i]] == -1 || $states[$groups[$i]] == 1)
	 { echo 'green_off'; }
	 if ($states[$groups[$i]] == -2)
	 { echo 'green_yellow'; } 
	 ?>.png" border=0 style="border:1px solid black; padding: 0;"></a>
	</td>
	<td>&nbsp;</td>
<?
		}
		} else { echo "<tr><td colspan=2>"; sas_pcode('error','Keine Gruppen vorhanden!'); echo "<td></tr>"; }
		echo "</table>";
	break;
 case "group":	/* innerhalb einer Gruppe gibt es fuer den normalen Benutzer nichts zu sehen */
	break;
 default:
	echo "Invalid Arguments!";
	break;
}
/* Seitenende */
ldap_close($ds);
?>
</form>
<br>Angezeigt wird der <b>aktuelle</b> Status.<br>
<br>
<a href="log-inetsave.php">Internet Save Log</a>

<?
sas_end();
