<?
include("../../sas.inc.php");
sas_start("Trafficaufstellung","../../","/modules/internet/",2);
sas_showmenu();
sas_varimport('range');

echo '<meta http-equiv="refresh" content="600">';
sas_pcode('attention','Aus welchen Gründen auch immer: es geht net ganz so wie\'s soll.');

if ($range == '') { $range = '10.'; }
$range = $range;
$range = addslashes($range);

if (substr_count($range,'.') < 3)
{

	$sql = 'SELECT SUBSTRING(ip,1,LOCATE(".",SUBSTRING(ip,'.strlen($range).'+1))+'.strlen($range).') AS iprange,FORMAT(SUM(traffic)/1024,0) FROM sas.trafficip WHERE ip LIKE "'.$range.'%" GROUP BY iprange';
} else {
	$sql = 'SELECT ip as iprange,FORMAT(SUM(traffic)/1024,0) FROM sas.trafficip WHERE ip LIKE "'.$range.'%" GROUP BY ip';
}
	mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
	$result = mysql_query($sql);
	echo mysql_error();


qbe_web_maketable(true);
?>
<tr><th>Range</th><th>Traffic</th></tr>
<?php

$alt = FALSE;
$col = "";
while ($row = mysql_fetch_row($result))
{
	$alt = !$alt;
	if ($alt) { $col = "#004E89"; } else { $col = ""; }
	if (strrchr($row[0],'.') == 0) { $setlink = TRUE; } else { $setlink = FALSE; }
	
	qbe_web_maketr();
	
/*	?>
	<tr bgcolor="<?=$col?>" <?php 
	if ($setlink) { ?>onClick="location.href='<?=$PHP_SELF?>?range=<?=$row[0]?>'" style="cursor: normal;" <?php }
	
	?>> */?><td><?php
	
	if ($setlink) 
	{ ?><a href="<?=$PHP_SELF?>?range=<?=$row[0]?>"><?=$row[0]?></A><?php }
	else
	{ ?><a href="http://10.0.0.2:3000/<?=$row[0]?>.html"><?=$row[0]?></a><?php }
	
	?>
	</td><td align=right><?=$row[1]?> kB</td></tr>
	<?php
}

echo '</table><br><br>Update alle 5 Minuten, nur <b>nicht angemeldete</b> Benuter.<br>';

sas_end();
