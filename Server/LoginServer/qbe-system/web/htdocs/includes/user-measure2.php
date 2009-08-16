<?
	require_once('user-measure.php');
	function usermeasure($userid,$showmacip = 0) 
	{
if (isset($userid)) {
if ($userid != "") {

	$diskabsolute = getdiskspace($userid);
	$diskpercent = intval(($diskabsolute / 20) * 100);

	$trafficabsolute = gettraffic($userid);
	$trafficpercent = intval(($trafficabsolute / 150) * 100);
	?>
	<table border=0 cellpadding=0 cellspacing=0 class=white>
	<? if ($showmacip == 1) { ?>
	<tr class=white>
		<td class=white>&nbsp;</td>
		<td class=white>IP:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=sas_web_getclientip();?></td>
		<td class=white>&nbsp;</td>
		<td class=white colspan=3><!--registered to: <? $reggeduser = lookuphostip(sas_web_getclientip()); if ($reggeduser == $userid) { echo "you"; } else { echo "<b>".$reggeduser."</b>"; } ?>--></td>
		<td class=white>&nbsp;</td>
	</tr>
	<tr class=white>
		<td class=white>&nbsp;</td>
		<td class=white>MAC:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=sas_web_getclientmac(sas_web_getclientip());?></td>
		<td class=white>&nbsp;</td>
		<td class=white colspan=3><!--<? 
			$reggedmacuser = lookupmac(sas_web_getclientmac(sas_web_getclientip()));
			if ($reggedmacuser == $reggeduser) { echo "the same..."; } else { echo "registered to: ".$reggedmacuser; }
			?>--></td>
		<td class=white>&nbsp;</td>
	</tr>
	<? } ?> 
	<tr class=white>
		<td class=white>&nbsp;</td>
		<td class=white>Disk:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=$diskabsolute?> MB / <?=$diskpercent?>%</td>
		<td class=white>&nbsp;</td>
		<td class=white><img src="/graphics/bar-edge-left.png"></td>
		<td class=white><?
	
		if($diskpercent>100) {$diskpercent = 100; }
		$percent = $diskpercent*2;
		
		$count = $percent / 10;
		$imgcount = 0;
		if ($diskpercent > 0)
		{	$img = '<img height="15" src="/graphics/bar-slice-filled.png" border="0">';
			for ($i = 0; $i<$count; $i++)
			{	echo $img; }
			$imgcount = $i;
		}
		if ($diskpercent < 100)
		{
			echo '<img height="15" src="/graphics/bar-filler.png" width="';
			echo 200-($imgcount*10) ."\">"; }
		?></td>
		<td class=white><img src="/graphics/bar-edge-right.png"></td>
		<td class=white>&nbsp;</td>
	</tr>
	<tr class=white>
		<td class=white>&nbsp;</td>
		<td class=white>Traffic:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=$trafficabsolute?> MB / <?=$trafficpercent?>%</td>
		<td class=white>&nbsp;</td>
		<td class=white><img src="/graphics/bar-edge-left.png"></td>
		<td class=white><?

		if ($trafficpercent>100) {$trafficpercent = 100; }
		$percent=$trafficpercent*2;
	
		$count = $percent / 10;
		$imgcount = 0;
		if ($trafficpercent > 0)
		{	$img = '<img height="15" src="/graphics/bar-slice-filled.png" border="0">';
			for ($i = 0; $i<$count; $i++)
			{	echo $img; }
			$imgcount = $i;
		}
		if ($trafficpercent < 100)
		{
			echo '<img height="15" src="/graphics/bar-filler.png" width="';
			echo 200-($imgcount*10) ."\">"; }
		?></td>
		<td class=white><img src="/graphics/bar-edge-right.png"></td>
		<td class=white>&nbsp;</td>
	</tr>
	</table>
	<br/>	
<?}}}
 
