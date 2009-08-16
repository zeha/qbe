<?php
	// Module Defines

	$qbe_modules['changelog'] = array('desc' => 'Arbeitsliste f&uuml;r Administratoren', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler');

	function qbe_module_changelog_init_app_menu()
	{	global $qbe_app_menu;

		$qbe_app_menu['_top'][] = array( 'link' => '/modules/changelog', 'prereq' => 'login', 'prereq-group' => 'sysops', 'text' => 'System Changelog' );
	}

