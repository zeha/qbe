<?php
	require('../../sas.inc.php');
	sas_start("IP Lookup","../../","/modules/computer",0);
	
	sas_varimport('ip');
	if ($ip != '')
	{
		echo sas_web_getclientmac($ip);
	}

	sas_end();
