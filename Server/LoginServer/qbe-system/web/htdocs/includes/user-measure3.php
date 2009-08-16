<?
	require_once('user-measure.php');
	function usermeasure3($userid) 
	{
if (isset($userid)) {
if ($userid != "") {

	$diskabsolute = getdiskspace($userid);
	$diskpercent = intval(($diskabsolute / 20) * 100);

	$trafficabsolute = gettraffic($userid);
	$trafficpercent = intval(($trafficabsolute / 150) * 100);
	?>
	<table border=0 cellpadding=0 cellspacing=0>
	<tr class=white>
		<td class=white>Disk:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=$diskpercent?>%</td>
	</tr>
	<tr class=white>
		<td class=white>Traffic:</td>
		<td class=white>&nbsp;</td>
		<td class=white><?=$trafficpercent?>%</td>
	</tr>
	</table>
<?}}}

