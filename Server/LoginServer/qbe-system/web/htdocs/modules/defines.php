<?php

	$qbe_providers = array();

	// The Core
	require('core/defines.php');

	// MODULES
	if (!$qbe_is_rpccall)
	{
		require('sysinfo/defines.php');
		require('client/defines.php');
		require('changelog/defines.php');
		require('computer/defines.php');
		require('filexs/defines.php');
		require('redir/defines.php');
	}
	
	require('ldap/defines.php');

	if (!$qbe_is_rpccall)
	{
		require('internet/defines.php');
		require('testenv/defines.php');
		require('rfid/defines.php');

		require('sis/defines.php');
	}

