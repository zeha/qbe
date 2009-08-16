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
		<td class=white><img src="/graphics/statusbar_l.png"></td>
		<td class=white><?

		if ($diskpercent > 0)
		{
			echo '<img height="16" src="/graphics/statusbar_m.png" width="';
			if ($diskpercent > 100) { echo "200"; } else { echo $diskpercent*2; }
			echo '">';
		}
		if ($diskpercent < 100)
		{
			echo '<img height="16" src="/graphics/statusbar_gr_m.png" width="';
			echo (200-($diskpercent*2)) ."\">"; }
		?></td>
		<td class=white><img src="/graphics/statusbar_r.png"></td>
		<td class=white>&nbsp;</td>
	</tr>
	<tr class=white>
		<td class=white>&nbsp;</td>
		<td class=white>Traffic:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=$trafficabsolute?> MB / <?=$trafficpercent?>%</td>
		<td class=white>&nbsp;</td>
		<td class=white><img src="/graphics/statusbar_l.png"></td>
		<td class=white><?

		if ($trafficpercent > 0)
		{
			echo '<img height="16" src="/graphics/statusbar_m.png" width="';
			if ($trafficpercent > 100) { echo "200"; } else { echo $trafficpercent*2; }
			echo '">';
		}
		if ($trafficpercent < 100)
		{
			echo '<img height="16" src="/graphics/statusbar_gr_m.png" width="';
			echo (200-($trafficpercent*2)) ."\">"; }
		?></td>
		<td class=white><img src="/graphics/statusbar_r.png"></td>
		<td class=white>&nbsp;</td>
	</tr>
	</table>
	<br/>	
<?}}} ?>
 
