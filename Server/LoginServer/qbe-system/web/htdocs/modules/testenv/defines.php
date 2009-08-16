<?php
	// Module Defines
	$qbe_modules['testenv'] = array('desc' => 'Testumgebung f&uuml;r den Unterricht', 'copyright' => '&copy; Copyright 2004 Wolfgang Bauer', 'version' => '0.1');

	function qbe_module_testenv_init_app_menu()
	{	global $qbe_app_menu, $userid, $user;

		$qbe_app_menu['testenv'] = array(
			'_section' => array( 'title' => 'Testumgebung', 'prereq' => 'login', 'prereq-group'=>'testarea'),
			array('text'=>'Erstellen','link'=>'/modules/testenv/erstellen', 'prereq-group'=>'testarea'),
			array('text'=>'Angabe hochladen','link'=>'/modules/testenv/angabeup', 'prereq-group'=>'testarea'),
			array('text'=>'Abgabe','link'=>'/modules/testenv/abgabe'
			, 'prereq-group'=>'testarea')
		
		
		
		);
	}

