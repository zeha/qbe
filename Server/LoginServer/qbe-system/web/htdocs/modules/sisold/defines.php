<?php
	// Module Defines

	$qbe_modules['sis'] = array('desc' => 'Schulinformationssystem', 'copyright' => '&copy; Copyright 2001-2002 Ulrich Moshammer &amp; 2003-2004 Christoph Piribauer'); 

	function qbe_module_sis_init_app_menu()
	{	global $qbe_app_menu, $userid, $user;

		$qbe_app_menu['sis'] = array(
			'_section' => array( 'title' => 'Infosystem', 'prereq' => 'login'),
			
			array('text'=>'Kalender','link'=>'/modules/sis/cal2.php'),
			array('text'=>'Infos','link'=>'/modules/sis/news.php'),
			array('text'=>'AV-Stellvertreter', 'link'=>'/modules/sis/av.php'),
			array('text'=>'Stundenplan', 'link'=>'/modules/sis/stundenplan.php'),
		);

	}

