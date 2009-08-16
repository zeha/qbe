<?php
	// Module Defines

	$qbe_modules['computer'] = array('desc' => 'LDAP Computerverwaltung', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler');

	function qbe_module_computer_init_app_menu()
	{	global $qbe_app_menu;

		$qbe_app_menu['computer'] = array(
			'_section' => array( 'title' => 'Computer', 'prereq' => 'login' ),
			
			array('text'=>'Notebooks','link'=>'/admin/tools/request_clearance?action=listopen','prereq'=>'login','prereq-group'=>'notebookadm'),
	//		array('text'=>'Saal-PCs','link'=>'/admin/admin/addclient_nt','prereq'=>'login','prereq-group'=>'halladm'),
			array('text'=>'Eigener Laptop','link'=>'/admin/tools/request_clearance','prereq'=>'login'),
			array('text'=>'Stand-Computer','link'=>'/modules/computer/manage-clients','prereq'=>'login','prereq-group'=>'teachers'),
			array('text'=>'Remote Shutdown','link'=>'/modules/computer/remote-shutdown','prereq'=>'login','prereq-group'=>'userinetchange')
		);

	}

