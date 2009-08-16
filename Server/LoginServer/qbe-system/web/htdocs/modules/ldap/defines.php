<?

$qbe_modules['ldap'] = array('desc' => 'Provider "user": Novell eDirectory', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler');
$qbe_providers['user'] = 'ldap';

$qbe_report_templates['class'] = '/modules/ldap/report/search?name=Klassenliste&f_base=%28%26%28objectClass%3DInetOrgPerson%29+%28ou%3D&f_end=))&value=';
$qbe_report_templates['class-list'] = $qbe_report_templates['class']; // DEPRECATED
$qbe_report_templates['user-by-uid'] = '/modules/ldap/report/search?name=User-ID&f_base=uid%3D&f_end=&value=';
$qbe_report_templates['user-by-sn'] = '/modules/ldap/report/search?name=Nachname&f_base=sn%3D&f_end=&value=';
$qbe_report_templates['lookup-class'] = '/modules/ldap/report/search?name=Klassen&f_base=%28%26%28objectClass%3DorganizationalUnit%29+%28ou%3D&f_end=))&fields=ou&value=';

function qbe_module_ldap_init_app_menu()
{
	// Menu Builder
	global $qbe_app_menu;

	array_push($qbe_app_menu['user'], 
	
		array( 'text' => 'Benutzer verwalten', 'link' => '/modules/ldap/manage-objects', 'prereq-group' => 'useradm|passchange'),
//		array( 'text' => 'Klassen verwalten', 'link' => '/admin/admin/classes', 'prereq-group' => 'groupadm'),
		array( 'text' => 'Berichte', 'link' => '/modules/ldap/report/list', 'prereq-group' => 'useradm')
		
	);

}

function qbe_module_ldap_init_app_adminpage()
{
	global $qbe_app_adminpage;
	array_push($qbe_app_adminpage['user'],
		array( 'text' => 'Passw&ouml;rter &auml;ndern', 'link' => '/modules/ldap/manage-objects', 'prereq-group' => 'passchange')
	);
}

/* eof */

