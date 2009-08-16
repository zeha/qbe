<?php
	$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
/*	$iframe = true;
	
	$headers = getallheaders();
	if (isset($headers['User-Agent']))
	{	// fucking msie!
		if (strstr($headers['User-Agent'],'MSIE'))
		{
			$iframe = false;
		}
	}
*/
	$iframe=false;
	if ($iframe)
	{
	/* iframe */
	
	require("../../sas.inc.php");
	sas_start("Qbe SAS","../../","/modules/redir/",1);
	sas_showmenu();

	if ($url == '') { echo '<meta http-equiv="refresh" content="0; url=/">'; }

	?>
	<iframe src="<?=$url?>" style="width: 100%; height: 100%;">
		<a href="<?=$url?>">Hier klicken.</a><br />
	</iframe>
	<?

	sas_end();

	} else {
		header("Location: ".$url);
	}
