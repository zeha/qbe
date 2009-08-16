<?php
	require '../../sas.inc.php';
	require '../../modules/client/version.php';
	
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
	switch ($mode)
	{
		case 'startup':
			$frameurl = 'http://127.0.0.1:7666/web/hta-login';
			break;
		default:
			$frameurl = 'http://127.0.0.1:7666/web/menu';
	}
	
	if (strstr($sas_client_ip,'10.10.'))
	{
		$frameurl = 'info-ip.php';
	}

	$version = isset($_REQUEST['ver']) ? $_REQUEST['ver'] : '&szlig';
	$user_v = str_replace('.','',$version);
	$sys_v = str_replace('.','',$qbe_ilogin2_version);
	$last_v = str_replace('.','',$qbe_ilogin2_current);
	if ($user_v < $sys_v)
	{
		$frameurl = 'info-update.php';
	}
	if ( ($user_v > $sys_v) && ($user_v < $last_v) )
	{
		$frameurl = 'info-update.php?skipto='.urlencode($frameurl);
	}
	
	$today = getdate();
#	print_r($today);
	if ($today['yday'] > 347)
	{
		$frameurl = 'special.php?url='.$frameurl;
	}
	
?>
<html>

<title>Qbe SAS Client <?=$version?></title>

<frameset rows="50,*" frameborder=no border=0>

<frame src="svc-top.php?ver=<?=$version?>" APPLICATION="yes" scrolling=no noresize>
<frame src="<?=$frameurl?>" APPLICATION="yes" noresize>

</frameset>
</html>
