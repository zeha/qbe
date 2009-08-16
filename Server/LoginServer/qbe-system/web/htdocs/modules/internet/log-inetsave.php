<?
include("../../sas.inc.php");
sas_start("InetLock Log","../../","/statistics/",2);
sas_showmenu();
?>

<style>
	td.loglist { font-size: 9pt; }
</style>
<!-- meta http-equiv="refresh" content="25;" -->
<?php

importVar("startline");
importVar("filterip");

if($startline==0) {$startline=0;}

	$whereclause = '';
	if ($filterip != '') { $whereclause = 'WHERE userip=\''.addslashes($filterip).'\' '; }
	$sql = 'SELECT * FROM inetsavelog '.$whereclause.' ORDER BY saved LIMIT '.$startline.','.($startline+50);

	qbe_web_maketable(true);
	
	$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
	mysql_select_db($sas_mysql_database);
	$res = mysql_query($sql);

?>
<tr>
<th>Benutzer</th><th>IP-Adresse</th><th>Klasse</th><th>Gruppe</th><th>Datum/Zeit</th><th>Status</th></tr>
</tr>
<?

#$log = file("/sas/log/inetsave.log");
#if (!isset($_GET['start'])) { $startline = sizeof($log)-25; } else { $startline=intval($_GET['start']); }

function importVar($varname,$default = "")
{	global $$varname;
	if (!isset($_GET[$varname]))
	{ $$varname = $default; } else { $$varname = $_GET[$varname]; }
}
$thisline=0;
while ($row = mysql_fetch_array($res))
{	$thisline++;
	//	list($lip,$luser,$luid,$lclass,$lgroup,$ldate,$ltime,$lstatus) = split(";", $line);
		$lip = $row['userip'];
		$luser = $row['userdn'];
		$lclass = $row['class'];
		$lgroup = $row['classgroup'];
		$ldate = $row['saved'];
		$lstatus = $row['newstate'];
	
	//	if (($filterip != "") && ($filterip != $lip)) { $show = FALSE; }

	qbe_web_maketr();
	?>
	<td class=loglist><?=$luser?></td>
	<td class=loglist><a href="<?=$PHP_SELF?>?startline=<?=$startline?>&filterip=<?=$lip?>"><?=$lip?></a></td>
	<td class=loglist><?=$lclass?></td>
	<td class=loglist><?=$lgroup?></td>
	<td class=loglist><?=$ldate?></td>
	<td class=loglist><?=$lstatus?></td>
	</tr><?

}

?>
</table>
<form method=get action="<?=$PHP_SELF?>">
<center>
<?
	$res = mysql_query('SELECT COUNT(*) FROM inetsavelog');
	$row = mysql_fetch_row($res);
	
	if ($startline > 0)
	{ $linkline = $startline-50; if ($linkline < 0) { $linkline=0; }
		?>
		<a href="<?=$PHP_SELF?>?startline=0">|&lt;</a> 
		<a href="<?=$PHP_SELF?>?startline=<?=$linkline?>">&lt;&lt;</a><? 
	}
	?> <input type=text size=5 name=startline value="<?=$startline?>">/<?=$row[0]?> <?
	if ($thisline>($startline+50))
	{
		
		?>
		<a href="<?=$PHP_SELF?>?startline=<?=$thisline+50?>">&gt;&gt;</a>
		<a href="<?=$PHP_SELF?>?startline=<?=$row[0]-25?>">&gt;|</a>
		<?
	}
	
?>
</center>
</form>

<?	mysql_close($db);
	sas_end();

