<?

$qbe_modules['core'] = array('desc' => 'Qbe Application Server', 'copyright' => '&copy; Copyright 2001-2004 Christian Hofst&auml;dtler','version' => $sas_version);

function qbe_module_core_init_app_adminpage()
{
	global $qbe_app_adminpage;
	$qbe_app_adminpage['core'] = array(
		'_section' => array('icon' => '/graphics/icons-modules.png', 'prereq-group' => 'sysops'),
		array( 'link' => '/modules/core/modmgr', 'text' => 'Module verwalten'),
		array( 'link' => '/modules/core/about', 'text' => 'Versionsinformation'),
	);
	$qbe_app_adminpage['user'] = array(
		'_section' => array('icon' => '/graphics/adminpage/security.png'),
		array( 'text' => 'Eigenes Passwort &auml;ndern', 'link' => '/modules/core/chpass')
	);
}

function qbe_module_core_init_app_menu()
{
	// Menu Builder
	global $qbe_app_menu,$userid,$user;

	$qbe_app_menu['_top'] = array( 
	
		'_section' => array (),
		
		array( 'link' => '/modules/redir/ssl?url=/modules/core/checklogin', 'prereq' => 'nologin', 'text' => 'Anmelden'),
//		array( 'mode' => 'code', 'function' => 'qbe_module_core_app_menu_code_loggedoutmenu', 'prereq' => 'nologin')
//		array( 'link' => '/forum/news', 'prereq' => 'login', 'text' => 'Neuigkeiten' )
		
	);

	$qbe_app_menu['_user'] = array( 
	
		'_section' => array ( 'prereq' => 'login', 'title' => $userid ),
		array( 'link' => '/modules/core/logout', 'prereq' => 'login', 'text' => '<b>Abmelden</b>' ),
		array( 'mode' => 'text', 'text' => ''),
		
		array( 'link' => '/modules/redir/outside?url='.urlencode('http://webmail.htlwrn.ac.at/horde/imp/login.php?imapuser='.$userid.'&new_lang=de_DE&url=http://webmail.htlwrn.ac.at/horde/'),  'prereq' => 'login', 'prereq-group' => 'teachers', 'text' => 'Webmail' ),
		array( 'link' => '/modules/core/datenschutz', 'prereq' => 'login', 'text' => 'Datenschutz'),
		array( 'link' => '/modules/core/chpass', 'prereq' => 'login', 'text' => 'Passwort &auml;ndern')
		
	);

	$qbe_app_menu['statistics'] = array(
		'_section' => array ( 'prereq' => 'login', 'title' => 'Statistiken'),

		array( 'mode' => 'code', 'function' => 'qbe_module_core_app_menu_code_stats', 'prereq' => 'login')
	);

	$qbe_app_menu['user'] = array(
		'_section' => array ( 'prereq' => 'login', 'title' => 'Benutzer' )
		);

}

require_once($qbe_http_basepath.'/includes/user-measure3.php');

function qbe_module_core_app_menu_code_stats()
{	global $userid;
	usermeasure3($userid);
}


function qbe_module_core_app_menu_code_loggedoutmenu()
{	global $qbe_http_globalservername;
	$n = "\n";
	echo '<br /><form method="post" action="https://'.$qbe_http_globalservername.'/admin/login.php">'.$n;
	echo '<script type="text/javascript">function qbemodulecoreappmenuloggedoutmenudisable() { var o=document.getElementById(\'menuloginbutton\'); o.textContent="wait..."; o.style.backgroundColor="#333"; document.getElementById("menuloginuser").style.backgroundColor="#333"; document.getElementById("menuloginpass").style.backgroundColor="#333";}</script>'.$n;
	echo '<table style="padding: 0 0 0 0; margin: 0 0 0 0;" cellpadding="0" cellspacing="0" class="borderoff"><tr><td>user:</td><td><input type="text" name="user" class="qbemodulecoreappmenuloggedoutmenu" id="menuloginuser" /></td></tr>'.$n;
	echo '<tr><td>pass:</td><td><input type="password" name="pass" id="menuloginpass" class="qbemodulecoreappmenuloggedoutmenu" /></td></tr>'.$n;
	echo '<tr><td></td><td><button type="submit" style="width:70px;" id="menuloginbutton" name="menuloginbutton" onclick="javascript:qbemodulecoreappmenuloggedoutmenudisable();">login</button></td></tr></table></form>'.$n;
}
/*menuloginbutton.innerHtml=\'wait...\';*/
/* eof */

