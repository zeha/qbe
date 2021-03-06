<?php
	// Module Defines

	$qbe_modules['rfid'] =
		array(
		'desc' => 'Qbe RFID Modul',
		'copyright' => '&copy; Copyright 2004 Mewald Matthias');
	function qbe_module_rfid_init_app_menu()
	{	global $qbe_app_menu, $userid, $user;

		$qbe_app_menu['rfid'] = array(
			'_section' => array( 'title' => 'Labor-Inventar', 'prereq' => 'login', 'prereq-group' => 'rfidadm' ),
			
			array('text'=>'Absent','link'=>'/modules/rfid/absent'),
			array('text'=>'Reservierung','link'=>'/modules/rfid/cybershelf'),
			array('text'=>'60min Log','link'=>'/modules/rfid/log60'),
			array('text'=>'Ger&auml;tedatenbank','link'=>'/modules/rfid/db'),
			array('text'=>'Log','link'=>'/modules/rfid/log'),
			array('text'=>'myAccounting','link'=>'/modules/rfid/accounting'),
			array('text'=>'Search Device','link'=>'/modules/rfid/searchdevice.php')
			
		);

	}


	function qbe_module_rfid_init_app_adminpage()
		{
		global $qbe_app_adminpage;
		$qbe_app_adminpage['rfid'] = array(
			'_section' => array('prereq'=>'login','icon'=>'/graphics/adminpage/rfid'), #'icon'=>'/modules/rfid/rfidicon'),
			array('mode'=>'code','function'=>'qbe_module_rfid_adminpage_box')
			);
		}
	
	function qbe_module_rfid_adminpage_box()
		{global $sas_mysql_server, $sas_mysql_user, $sas_mysql_password;
		$rc = 0;
		 $link = mysql_connect($sas_mysql_server, $sas_mysql_user, $sas_mysql_password);
		      mysql_select_db("rfid");
		
		global $userid;
		$uname = $userid;
		$numb = mysql_query ("Select GID from deviceentlehnung join entlehnunglog on deviceentlehnung.eid = entlehnunglog.eid where entlehnunglog.uid = '" .$uname . "' AND tstmp_hy IS NULL;");
		$kontonumber = mysql_num_rows($numb);
		if ($kontonumber){
			print ("Sie haben " .$kontonumber . " Ger&auml;te ausgeliehen<br>");
			$rc = 1;
			}
		
		$twentyfour = mysql_query ("Select gid from deviceentlehnung join entlehnunglog on deviceentlehnung.eid = entlehnunglog.eid where entlehnunglog.tstmp_bye <= NOW() - 86400 AND tstmp_hy IS NULL;");	
		$twentyfourcount = mysql_num_rows($twentyfour);
		if ($twentyfourcount){
			print("Sie haben " . $twentyfourcount . " Ger&auml;te l�nger als 24h ausgeliehen");
			$rc = 1;
		}
		return $rc;
		}

		
