<?php
	$qbe_modules['sysinfo'] = array('desc' => 'Server Health Display', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler');

	function qbe_module_sysinfo_init_app_menu()
	{	global $qbe_app_menu;
		array_push($qbe_app_menu['statistics'],
			array( 'text' => 'Server-Info', 'link' => '/modules/sysinfo/', 'prereq' => 'login', 'prereq-group' => 'sysinfo')
		);
	}
