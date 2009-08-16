<?php
	$qbe_modules['filexs'] = array('desc' => 'Dateizugriffsmodul', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler');

	function qbe_module_filexs_init_app_menu()
	{	global $qbe_app_menu;
		$qbe_app_menu['filexs'] = array(
					'_section' => array ( 'title' => 'Ablagen', 'prereq' => 'login' ),
					array( 'link' => '/modules/filexs/?show=own', 'text' => 'Eigene', 'prereq' => 'login'),
					array( 'link' => '/modules/filexs/?show=group', 'text' => 'Gemeinsame', 'prereq' => 'login'),
					array( 'link' => '/modules/filexs/?show=common', 'text' => 'Alle', 'prereq' => 'login')
				);
	}
