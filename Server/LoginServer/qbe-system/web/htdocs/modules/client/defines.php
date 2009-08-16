<?php
	$qbe_modules['client'] = array('desc' => 'SAS Client RPC und Downloads', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler');

	function qbe_module_client_init_app_menu()
	{	global $qbe_modules;
		require 'version.php';
		$qbe_modules['client']['version'] = $qbe_ilogin2_current;
	}
