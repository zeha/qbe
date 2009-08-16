<?
include("../../sas.inc.php");
sas_start("Trafficaufstellung","../../","/modules/internet/",0);
sas_showmenu();
?>
<table>
<? $tablestyle="cellpadding=5 border=0 cellspacing=0 width=300 style=\"border: 1px solid black;\"";?>
<tr><td>
<table <?=$tablestyle?>>
<?
	error_reporting(15);
	$db = mysql_connect("qbe-sql.system.htlwrn.ac.at","sastraffic","htlits");
	
	$res = mysql_query("SELECT SUM(traffic) as SUMTR FROM sas.trafficview");
	$row = mysql_fetch_row($res);
	$maxtraffic = $row[0];
	$traffic['total'] = intval($maxtraffic/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "e"');
	$row = mysql_fetch_row($res);
	$traffic['E'] = intval($row[0]/1024/1024);
	
	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "a"');
	$row = mysql_fetch_row($res);
	$traffic['A'] = intval($row[0]/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "h"');
	$row = mysql_fetch_row($res);
	$traffic['H'] = intval($row[0]/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "w"');
	$row = mysql_fetch_row($res);
	$traffic['W'] = intval($row[0]/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "Adm"');
	$row = mysql_fetch_row($res);
	$traffic['adm'] = intval($row[0]/1024/1024);

	$traffic['rest'] = $traffic['total'] - $traffic['E'] - $traffic['A'] - $traffic['H'] - $traffic['W'] - $traffic['adm'];
# $redtraffic = $maxtraffic - $traffic['rest'];

?>
<style>
	.total { background-color: #222222; color: white; }
	.abte { background-color: navy; color: white; }
	.abta { background-color: darkred; color: white; }
	.abth { background-color: darkorange; }
	.wmr { background-color: darkgreen; }
	.adm { background-color: cyan; color: black; }
	.rest { background-color: magenta; color: black; } 
</style>

&nbsp; <b>HTL:</b><br>
<tr style="background-color: black; color: white;">
	<td width=60% class=total>Total</td>
	<td width=20% class=total align=right><?=intval($traffic['total'])?> MB</td>
	<td width=10% class=total>100%</td>
</tr>
</table>
&nbsp; <b>Abteilungen:</b><br>
<table <?=$tablestyle?>>
<tr>
	<td width=60% class=abta>Automatisierungst.</td>
	<td width=20% class=abta align=right><?=intval($traffic['A'])?> MB</td>
	<td width=10% class=abta>&nbsp;<?=sprintf("%02u",intval($traffic['A']/$traffic['total']*100))?>%</td>
</tr>
<tr>
	<td width=60% class=abte>Elektro- / Infot.</td>
	<td width=20% class=abte align=right><?=intval($traffic['E'])?> MB</td>
	<td width=10% class=abte>&nbsp;<?=sprintf("%02u",intval($traffic['E']/$traffic['total']*100))?>%</td>
</tr>
<tr>
	<td width=60% class=abth>Hochbau</td>
	<td width=20% class=abth align=right><?=intval($traffic['H'])?> MB</td>
	<td width=10% class=abth>&nbsp;<?=sprintf("%02u",intval($traffic['H']/$traffic['total']*100))?>%</td>
</tr>
<tr>
	<td width=60% class=wmr>Werkmeister</td>
	<td width=20% class=wmr align=right><?=intval($traffic['W'])?> MB</td>
	<td width=10% class=wmr>&nbsp;<?=sprintf("%02u",intval($traffic['W']/$traffic['total']*100))?>%</td>
</tr>
<tr>
	<td width=60% class=adm>Administration</td>
	<td width=20% class=adm align=right><?=intval($traffic['adm'])?> MB</td>
	<td width=10% class=adm>&nbsp;<?=sprintf("%02u",intval($traffic['adm']/$traffic['total']*100))?>%</td>
</tr>
<tr>
	<td width=60% class=rest>Andere</td>
	<td width=20% class=rest align=right><?=intval($traffic['rest'])?> MB</td>
	<td width=10% class=rest>&nbsp;<?=sprintf("%02u",intval($traffic['rest']/$traffic['total']*100))?>%</td>
</tr>
</table>
<br>
&nbsp; <b>Top 10:</b><br>
<table <?=$tablestyle?>>
<?

	$res = mysql_query("SELECT userid,traffic FROM sas.trafficview ORDER BY traffic DESC LIMIT 0, 10",$db);
	while ($row = mysql_fetch_row($res))
	{
	?>
	<tr style="background-color: #444;">
		<td width=60%><?php 
			if($row[0] == "nobody") 
			{
			?><a href="stats-traffic-iprange"><i>unknown</i></a><?
			} else {
			?><a href="<?=$qbe_report_templates['user-by-uid'].$row[0]?>"><?=$row[0]?></a><? 
			}
		?></td>
		<td width=20% align=right><?=intval($row[1]/1024/1024)?> MB</td>
		<td width=10%>&nbsp;<?=sprintf("%02u",intval($row[1]/$maxtraffic*100))?>%</td>
	</tr>
	<?
	}
?>
</table>
</td><td><img src="stats-traffic-overall.chart.php"></td></tr>
</table>
<br>
Nach <a href="stats-traffic-iprange.php">IP-Bereichen</a><br>
<br>

<form method=get action="stats-traffic-class.php">
Nach Klasse: <input name=class size=5>&nbsp;<button type=submit>Anzeigen</button>
</form>

<?php
	sas_end();
