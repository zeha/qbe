<?php
	// Module Defines

	$qbe_modules['internet'] = array('desc' => 'Proxy Integration', 'copyright' => '&copy; Copyright 2001-2003 Andreas St&uuml;tzner &amp; 2001-2004 Christian Hofst&auml;dtler');

	function qbe_module_internet_init_app_menu()
	{	global $qbe_app_menu, $userid, $user;

		$qbe_app_menu['internet'] = array(
			'_section' => array( 'title' => 'Internet', 'prereq' => 'login'),
			
			array('text'=>'Freischaltung','link'=>'/modules/internet/','prereq-group'=>'inetlock'),
			array('text'=>'PC-Only','link'=>'/modules/internet/pconly','prereq-group'=>'inetrawpc'),
			array('text'=>'Traffic','link'=>'/modules/internet/stats-traffic-overall')
		);

		if ($userid != '')
		{
			global $sas_ldap_server, $sas_ldap_adminuser, $sas_ldap_adminpass;
			$ldap = ldap_connect($sas_ldap_server);
			ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );
			$l_list = ldap_read( $ldap , $user , "objectclass=*", Array("inetstatus") );
			$l_entry = ldap_first_entry( $ldap , $l_list );
			$l_attrs = ldap_get_attributes( $ldap , $l_entry );
			ldap_close($ldap);

			switch ($l_attrs['inetstatus'][0])
			{
			case 0:
				$text = 'on';
				break;
			case 1:
				$text = 'off';
				break;
			case 7:
				$text = 'prj';
				break;
			default:
				$text = '??';
				break;

			}

			$qbe_app_menu['_user']['_section']['right'] = 'Internet: '.$text;
		}

	}

