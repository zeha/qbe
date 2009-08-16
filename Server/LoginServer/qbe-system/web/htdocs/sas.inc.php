<?php
//
// Qbe SAS Application Server
// Copyright 2001, 2002, 2003, 2004 Christian Hofstaedtler
// sas.inc.php
//

//
// error handler stuff
function qbe_error_handler($errno, $errstr, $errfile, $errline) 
{
	$reporting = (($errno & error_reporting()) == $errno);
	# echo "$errno $errstr reporting: ".$reporting."<br>\n";
	if (!$reporting)
	{
		return;
	}
	if (!headers_sent())
	{
		header("HTTP/1.1 500 Internal Server Error");
	} else {
?>	<!-- " --></select></input></applet></embed></object>
	</small></form></h2></h3></h4></b></address></i></th></tr></td></table></em></div></div></div></div>
	</body></style></script>
	<br><br><br>
	</head></html>
	<?php 
	} 
	?>
	
	<html>
	<head>
	<title>Qbe Application Server</title>
	<style>
	BODY { background-color: white; color: black; }
	BODY,P,TD { font-family: "Trebuchet MS",Verdana,Helvetica; font-size: 12pt; }
	P,TD { color: white; }
	A,A:active,A:hover,A:visited { color: white; }
	table,tr,td,div,IMG { border: none; }
	</style>
	</head>
	<body>
	<center>
	<br><br><br>
	<div style="background-color: #336699; border: 1px solid black; width: 70%;">
	<br/>
	<table><tr><td><a href="/"><img src="/graphics/qbe.sas.topright.png" border="0"></a></td>
		<td><b style="color: white; font-weight: bold; font-size: 140%;">Systemfehler</b></td>
	</tr>
	<tr>	<td></td>
		<td style="font-weight: bold;">
			Der aktuelle Vorgang wurde aufgrund eines Fehlers abgebrochen.<br>
			Bitte versuchen Sie die Aktion zu einem sp&auml;teren Zeitpunkt erneut.<br>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="font-size: 60%;">
			<br/>
			Error: <?=$errstr?><br/>
			<? echo substr($errfile,strlen('/qbe/web/htdocs/')); ?> @ line <?=$errline?>
		</td>
	</tr>
	</table>
	<br/>
	</div>
	</center>
	</body></html>
<?php
	exit(1);
}
function qbe_error_handler_on()
{
	error_reporting(15);
}
function qbe_error_handler_off()
{
	error_reporting(0);
}

// set up error handler to our custom one
$qbe_error_handler_original = set_error_handler("qbe_error_handler");
// enable error reporting
qbe_error_handler_on();
// done, start...


require "/qbe/web/defines.php";

// init some state things
$qbe_app_menu = array();
$sas_cur_rootpath = "";
$sas_cur_pagepath = "";
$sas_client_ip = sas_web_getclientip();
$sas_wantmenu = 0;
$sas_wantheader = 1;
$sas_ldap_count = 0;

$qbe_is_rpccall = FALSE;

if (strstr($_SERVER['PHP_SELF'],'/rpc/') != '')
{
	$qbe_is_rpccall = TRUE;
}

// check if this is an rpc call - then we shouldnt redirect by default
if ( ( !isset($sas_no_redirect) ) && ($qbe_is_rpccall) )
{
	$sas_no_redirect = TRUE;
}

// normal page it seems
$sas_no_redirect = isset($sas_no_redirect) ? $sas_no_redirect : FALSE;

if ($sas_no_redirect == FALSE)
{
// if (strstr($_SERVER['HTTP_HOST'],$qbe_http_globaldomain) == "")
 if (strstr($_SERVER['HTTP_HOST'],$qbe_http_globalservername) == "")
 {
 	$url = 'http://'.$qbe_http_globalservername;
	if (strtolower($_SERVER['PHP_SELF']) != 'index.php')
		$url .= $_SERVER['REQUEST_URI'];
	header('Location: '.$url);
	print "\n\nRedirecting to $url\n";
	exit;
 }
}

$qbe_modules = array();


function sas_showmenu($whatmenu = 1)
{	global $sas_wantmenu;
	$sas_wantmenu = $whatmenu;
}

function sas_makehelplink($topic)
{
?>	<a href="#" onClick="javascript:popupform('/modules/help/?topic=<?=$topic?>');">Hilfe</a>
<?php
}

function qbe_web_makehr()
{
?>	<hr />
<?php
}

function qbe_web_makefixme()
{	global $user;
	if (sas_ldap_isadmin($user))
	{
		echo '<div style="width: 100px; height: 30px; background-color: red; color: white; font-weight: bold; text-align:center; font-size:15pt; border: 4px solid black; font-family:Trebuchet MS;">FIX ME!</div>';
	}
}

function qbe_web_maketable($border=false,$optional='')
{	global $sas_table_row;
	$sas_table_row=true;
?>	<table cellpadding=4 cellspacing=0 class="<?if($border){echo'borderon';}?>" <?=$optional?>>
<?
}
function qbe_web_maketr()
{	global $sas_table_row;
	$sas_table_row=(!$sas_table_row);
	echo '<tr class='.($sas_table_row ? 'r1' : 'r2').'>';
}

function qbe_web_makelookupform($field = 'uid',$subject = 'user')
{
?>	<a href="#" onClick="javascript:lookupform('/modules/core/lookup?subject=<?=$subject?>&popup=1+<?=$field?>','<?=$field?>');">Suchen</a>
<?php
}
function qbe_web_makelookupbutton($field = 'uid',$subject = 'user')
{
?>	<button onClick="javascript:lookupform('/modules/core/lookup?subject=<?=$subject?>&popup=1+<?=$field?>','<?=$field?>'); return false;" type=button>...</button>
<?
}

function qbe_web_makebox($contentsfunc,$size=0)
{	echo '<div class="box" style="'; 
	if ($size>0) {echo 'width: '.$size.'px;'; } echo '">';
	$contentsfunc();
	echo '</div>';
}

function sas_varimport($var)
{ global $$var; $$var = isset($_REQUEST[$var]) ? $_REQUEST[$var] : ''; }

function sas_ldap_getuid($user)
{
  global $sas_ldap_server, $sas_ldap_base, $sas_ldap_machineuser, $sas_ldap_machinepass,$sas_ldap_count;

	$sessionhash = 'ldap-uid-'.$user;
	if (isset($_SESSION[$sessionhash])) { return $_SESSION[$sessionhash]; }
  
  $sas_ldap_count++;
  
  $ds = @ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = @ldap_bind($ds,$sas_ldap_machineuser,$sas_ldap_machinepass);
        $list = @ldap_read ( $ds, $user , "objectClass=*" );
        $entry = @ldap_first_entry($ds, $list);
        $attrs = @ldap_get_attributes($ds, $entry);
	if (isset($attrs["inetstatus"])) { $is = $attrs["inetstatus"][0]; 
		if ($is == 3) { echo "Du nicht!"; exit; }
		@ldap_close($ds);
		return FALSE;
	}
	if (isset($attrs["uid"])) {
        $value = $attrs["uid"][0];
	} else { $value = ""; }
        @ldap_close($ds);
	$_SESSION[$sessionhash] = $value;
        return $value;
  }
}
function sas_ldap_getusername($uid)
{
	global $sas_ldap_server, $sas_ldap_base, $sas_ldap_machineuser, $sas_ldap_machinepass, $sas_ldap_count;
	$sas_ldap_count++;
	if (strchr($uid,'=') == FALSE)
			$user = sas_ldap_getdn($uid);
		else
		$user = $uid;
		
	$ds = @ldap_connect($sas_ldap_server);
	if ($ds)
	{
		$r = @ldap_bind($ds,$sas_ldap_machineuser,$sas_ldap_machinepass);
		$list = @ldap_read ( $ds, $user , "objectClass=*" );
		$entry = @ldap_first_entry($ds, $list);
		$attrs = @ldap_get_attributes($ds, $entry);
		if (isset($attrs["displayName"])) 
		{
			$value = $attrs["displayName"][0];
		} else { 
			$value = "-unknown-"; 
		}
		@ldap_close($ds);
		return $value;
	}
}

function sas_ldap_getdn($uid)
{
	return qbe_ldap_getobjectdn("uid=".$uid);
}

function qbe_ldap_getobjectdn($searchfilter)
{
	global $sas_ldap_server, $sas_ldap_base, $sas_ldap_machineuser, $sas_ldap_machinepass,$sas_ldap_count;
	
	if (isset($_SESSION['ldap-dn-'.md5($searchfilter)])) { return $_SESSION['ldap-dn-'.md5($searchfilter)]; }
	
	$sas_ldap_count++;
	
	$lr = ldap_connect($sas_ldap_server);
	if ($lr)
	{
	   $r = ldap_bind($lr,$sas_ldap_machineuser,$sas_ldap_machinepass);
	   $sr = ldap_search($lr,$sas_ldap_base,$searchfilter);
	   $sr2 = @ldap_first_entry($lr, $sr);
	   $dn = @ldap_get_dn($lr,$sr2);
	   @ldap_close($lr);
	   if ($dn)
	   {
	   	$_SESSION['ldap-dn-'.md5($searchfilter)] = $dn;
	   	return $dn; 
	   } else return false;
	} else return false;
}

function qbe_ldap_clearcache()
{
	foreach($_SESSION as $key => $value)
	{
		printf("<b>qbe_ldap_clearcache debug:</b> checking $key => $value...\n");
		if (stristr($key,"ldap-") != FALSE)
		{
			unset($_SESSION[$key]);
			printf("cleared.\n");
		} else {
			printf("dont care.\n");
		}
		printf("<br>");
	}
}

function sas_ldap_getuserdescription($uid)
{
global $sas_ldap_server, $sas_ldap_base, $sas_ldap_machineuser, $sas_ldap_machinepass,$sas_ldap_count;
$sas_ldap_count++;
$user = sas_ldap_getdn($uid);
$ds = @ldap_connect($sas_ldap_server);
if ($ds)
{
$r = @ldap_bind($ds,$sas_ldap_machineuser,$sas_ldap_machinepass);
$list = @ldap_read ( $ds, $user , "objectClass=*" );
$entry = @ldap_first_entry($ds, $list);
$attrs = @ldap_get_attributes($ds, $entry);
if (isset($attrs["description"])) {
$value = $attrs["description"][0];
} else { $value = "-unknown-"; }
@ldap_close($ds);
return $value;
}
}


function sas_ldap_isadmin($user)
{
/*
	global $sas_ldap_server,$sas_ldap_base, $sas_ldap_machineuser, $sas_ldap_machinepass,$sas_ldap_count;

	if (!isset($user)) return false;
	if ($user == "") return false;

	$sessionhash = 'ldap-admin-'.$user;
	if (isset($_SESSION[$sessionhash])) { return $_SESSION[$sessionhash]; }

	$sas_ldap_count++;
	

$ds = @ldap_connect($sas_ldap_server);
if ($ds)
{
	ldap_bind($ds,$sas_ldap_machineuser,$sas_ldap_machinepass);
	$dn = ldap_read ($ds, $user, "objectClass=*");
	$attrs = ldap_get_attributes($ds, ldap_first_entry($ds, $dn));
	ldap_close($ds);
	if (!isset($attrs['gidNumber'])) return false;
	if ($attrs['gidNumber'][0] == '200')
	{
		$_SESSION[$sessionhash] = true;
		return true;
	}
		else
	{
		$_SESSION[$sessionhash] = false;
		return false;
	}

} else return false;
*/
	return sas_ldap_isgroupmember('sysops',$user);
}
function sas_check_group($group)
{	global $user;
	return sas_ldap_isgroupmember($group,$user);
}
function sas_ldap_isgroupmember($group,$user)
{
global $sas_ldap_server,$sas_ldap_base, $sas_ldap_adminuser, $sas_ldap_adminpass,$sas_ldap_count;

	if (!isset($user)) return false;
	if ($user == "") return false;
	if (!isset($group)) return false;
	if ($group == "") return false;

	$sessionhash = 'ldap-group-'.md5($user.'-'.$group);
	if (isset($_SESSION[$sessionhash])) { return $_SESSION[$sessionhash]; }

	$sas_ldap_count++;

$ds = @ldap_connect($sas_ldap_server);
if ($ds)
{
	ldap_bind($ds); //,$sas_ldap_adminuser,$sas_ldap_adminpass);
	qbe_error_handler_off();
	$dn = ldap_read ($ds, "cn=".$group.",ou=group,ou=Administration,".$sas_ldap_base,"(member=".$user.")");
	$entries = ldap_get_entries($ds, $dn);
	qbe_error_handler_on();
	if ($entries["count"] > 0)
	{
		$_SESSION[$sessionhash] = true;
		return true;
	}
		else
	{
		$_SESSION[$sessionhash] = false;
		return false;
	}
	
} else return false;
}

function sas_changepassword($binduser,$bindpass,$user,$newpass)
{
global $sas_ldap_server,$sas_ldap_adminuser,$sas_ldap_adminpass;

$lr=ldap_connect($sas_ldap_server);
if ($lr) {
	$r=ldap_bind($lr,$sas_ldap_adminuser,$sas_ldap_adminpass);//$binduser,$bindpass);
#		$info["userpassword"]=array();
#		ldap_modify($lr,$user,$info);

	$info["userPassword"]=$newpass;
#	$hashes = exec(escapeshellcmd("/qbe/sbin/mkntpwd $newpass"));
#	$pos = strpos($hashes,":");
#	$info["sambaLMPassword"]=substr($hashes,0,$pos);
#	$info["sambaNTPassword"]=substr($hashes,$pos+1);
	$lm=ldap_modify($lr,$user,$info);

	if ($lm)
	{
#			ldap_bind($lr,$sas_ldap_adminuser,$sas_ldap_adminpass);
		$info=array();
		$hashes = exec(escapeshellcmd("/qbe/sbin/mkntpwd $newpass"));
		$pos = strpos($hashes,":");
		$info["sambaLMPassword"]=substr($hashes,0,$pos);
		$info["sambaNTPassword"]=substr($hashes,$pos+1);

		if (ldap_modify($lr,$user,$info))
		{
			return true; 
		} else {
			return false;
		}
	} else return false;
}

}

function sas_ldap_changepassword($user,$oldpass,$newpass)
{
sas_changepassword($user,$oldpass,$user,$newpass);
}

function sas_ldap_checkpassword($user,$pass)
{
	global $sas_ldap_server,$sas_ldap_count;
	
	$sas_ldap_count++;
	$lr = ldap_connect($sas_ldap_server);
	
	if ($lr)	// and sas_ldap_getdn($user))
	{
		qbe_error_handler_off();
		$r = @ldap_bind($lr,$user,$pass);
		qbe_error_handler_on();
		ldap_close($lr);
		if ($r)        return true; else return false;
	} else {
		echo "No Connection to LDAP or user not found!<br/>";
		return false;
	}
}

function sas_web_getclientip()
{
// check for request via proxy ...
$ip = getenv('HTTP_X_FORWARDED_FOR');
if ($ip == "")
	$ip = $_SERVER['REMOTE_ADDR'];

// egateway proxy
if ($ip == "10.0.0.1")
	$ip = "";
if ($ip == "")
	return false;
	else
	return $ip;
}
function sas_web_getclientmac($ip = false)
{
	global $sas_client_ip, $qbe_util_arp;
	$ret = false;
	
	if (!$ip)
		$ip = $sas_client_ip;
	if ($ip != "")
		$ip = $sas_client_ip;

	if ($ip != false)
	{
		$arpproc = popen($qbe_util_arp . escapeshellcmd($ip),'r');
		$arpoutp = fgets($arpproc,2048);
		$arpoutp = fgets($arpproc,2048);
		pclose($arpproc);
		$mac = substr($arpoutp,strpos($arpoutp,'ether')+5);
		$mac = trim($mac);
		$mac = substr($mac,0,strpos($mac,' '));
	
		if (strlen($mac) > 5)
		{
			$ret = $mac;
		} 
	}
	return $ret;
}

function sas_locallink($text,$href,$target,$params)
{
global $sas_cur_rootpath;
if ($params == "")
{	$pars = $SID; } else { $pars = $params . "&" . $SID; }
if ($target == "")
{ $target = "_self"; }
?><a href="<?=$sas_cur_rootpath?>/<?=$href?>?<?=$pars?>" target="<?=$target?>"><?=$text?></a><?
}
require '/qbe/web/defines.root.php';
function sas_start($title,$rootpath,$pagepath,$needlogin,$wantheader=1)
{
global $sas_cur_rootpath, $sas_cur_pagepath, $sas_version, $sas_codename;
global $user, $pass, $valid, $PHP_SELF, $userid, $QBE_COOKIE;
global $sas_global_pageinit, $pagetype, $sas_sslstate;
global $sas_wantmenu, $sas_wantheader, $qbe_popup;

	// set up state variables
	$qbe_popup = 0;
	$sas_wantheader = $wantheader;
	$sas_cur_rootpath = $rootpath;
	$sas_cur_pagepath = $pagepath;
	$PHP_SELF = $_SERVER['PHP_SELF'];
	$sas_sslstate = "off"; if (isset($_SERVER['HTTPS'])) { $sas_sslstate = $_SERVER['HTTPS']; }

	if (isset($_REQUEST['popup']))
	{
		if ($_REQUEST['popup'])
		{
			$qbe_popup = $_REQUEST['popup'];
			$sas_wantheader = 2;
			$sas_wantmenu = 0;
			if ($qbe_popup==2) { $sas_wantheader = 1; }
		}
	}

	if (!isset($pagetype)) {
		$pagetype = ""; 
	}
	
	$havelogin = 0;
	session_set_cookie_params(0);
	session_start();

if (isset($_SESSION['user']))	$user = $_SESSION['user'];  else $user  = "";
// if (isset($_SESSION['pass']))	$pass = $_SESSION['pass'];  else $pass  = "";
if (isset($_SESSION['valid']))  $valid = $_SESSION['valid']; else $valid = 0;
$_SESSION['pass'] = "";

	if ($valid == 1)
	{
		$havelogin = 1;
	} else {
		$_SESSION['user'] = ""; $_SESSION['pass'] = ""; $_SESSION['valid'] = 0;
		$havelogin = 0;
	}

	if ($user != "") 
	{
		$userid = sas_ldap_getuid($user); 
	} else {
		$userid = "";
	}

 if ($sas_wantheader > 0)
 {
 	$qcol = '';
 	if ($sas_wantheader == 2) { $headtitle = 'Qbe SAS: '.$title; } else { $headtitle = $title; }

	global $qstyle;
	sas_varimport('qstyle');
	
	echo '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">'."\n";
	echo '<head><title>'.$headtitle.'</title>';
	echo '<link rel="SHORTCUT ICON" href="/graphics/favicon.ico" /><link rel="StyleSheet" type="text/css" href="/graphics/style.css" />'."\n";
	if (!isset($_SESSION['qstyle'])) {$_SESSION['qstyle']=0;}
	if ($qstyle != '') {$_SESSION['qstyle'] = intval($qstyle);}
	$qs = $_SESSION['qstyle']; $qd=getdate(); if(($qd['yday']>173)&&($qd['yday']<183)) $qs=2;
	if ($qs==2) { $qcol='#ffcc00'; } if ($qs==3) { $qcol='#cc3300'; } if ($qs==4) {$qcol='white'; }
	if ($qs!=0) { 
		echo '<style>body { background-color: '.$qcol.'; color: black; }';
		echo "a,a:hover { color: black; }\n#layoutright a { color: black; }\n#layoutright { color: black; }</style>"; 
		echo '<style>tr.r1 td,tr.r2 td,th,th a,tr.r1 td a,tr.r2 td a { color: white; } img,input { border: 1px solid black;} img.icon { border: none;} ';
		echo '.error:before,.done:before {color:black;} </style>'; 
	} $_SESSION['qstyle']=$qs;
	echo '<script src="/graphics/js-base.js" type="text/javascript"></script>';

	?>
	</head>
	<body>
	
	<?php 

	if ($sas_wantheader != 2) 
	{	// non-popup mode
		$sas_wantmenu = 1;
		$sas_wantheader = 1;
		?>
		<div id="layouttop">
			<h1><?=$title?></h1>
		</div>
		<?php
	}
	
	?>
	<!-- content start -->
	<div id="layoutmiddle">
	<?php
	
	// Logged In ?
	if (($needlogin >= 1) && ($havelogin != 1))
	{
		?>
		<meta http-equiv="refresh" content="0; url=/modules/core/checklogin.php?url=<?=urlencode($_SERVER['REQUEST_URI'])?>" />
		<big>Keine Anmeldung / Timeout </big><br/>
		<br/>
		Sie sind nicht angemeldet oder Ihre Anmeldung liegt zuweit zur&uuml;ck und wurde aus Sicherheitsgr&uuml;nden entfernt! Bitte melden Sie sich neu.
		Neu <a href="/modules/core/checklogin.php?url=<?=urlencode($_SERVER['REQUEST_URI'])?>">Anmelden</a>.
		<?
		sas_end(); exit();
	} 	// end needlogin 

	// ok, we are logged in.
	
	// this is the first possible point for the menu to initialize; we also need it below..
	// Start Menu Builder
	qbe_modules_call('init_app_menu');

	// require Admin ?
	if ($needlogin == 2) { 
		if (!sas_ldap_isadmin($user)) { 
			sas_pcode('error','Unbefugter Zugriff.'); 
			sas_showmenu(); 
			sas_end(); exit(); 
		}
	}	// end need admin

 } else {

	// Logged In ?
	if (($needlogin >= 1) && ($havelogin != 1))
	{
		echo "Timeout"; exit();
	}	// end timeout check
 }

 // end sas_start
	
	if (isset($_SESSION['qstyle']))
	if ($_SESSION['qstyle'] > 0)
	{?>
	<style>
	#layoutright img { border: none; }
	.qbox { border: 1px solid black; }
	</style>
	<?php
	} 
}

function sas_end($exit = true)
{
	global $sas_cur_rootpath, $sas_wantmenu, $sas_phpext, $userid, $user, $sas_need_ilogin;
	global $sas_wantheader, $qbe_popup;
	global $sas_version, $sas_codename, $sas_client_ip, $qbe_ssl, $qbe_servername, $qbe_http_basepath, $sas_sslstate;
	function sas_showcopyright()
	{
		global $qbe_http_globalservername, $sas_ldap_count, $sas_version, $sas_sslstate, $qbe_servername; ?>
		<div id="bottomline"><br/><br/><a href="/modules/core/about.php">Qbe SAS <?=$sas_version?></a> &copy; 2001-2004 Christian Hofst&auml;dtler &amp; Andreas St&uuml;tzner.<br /><a href="https://<?=$qbe_http_globalservername?>/">SSL:</a> <?=($sas_sslstate == "off") ? "Inaktiv." : "Aktiv."; ?> <?=$qbe_servername?>
		</div> <?php
	}
	if ( ($sas_wantheader) && ($sas_wantheader != 2) ) { sas_showcopyright(); }
	$p = "/admin/"; $x = $sas_phpext;
	if ($qbe_popup) { $sas_wantmenu=0; }
	echo "</div>";
	if ($sas_wantheader)
	{
		switch ($_SESSION['qstyle'])
		{
		case 2:
			echo "<div id=\"layoutright\"><a href=\"/\"><img src=\"/graphics/qbe.sas.topright.yellow.png\" alt=\"Qbe SAS\" title=\"\" /></a>";
			break;
		case 3:
			echo "<div id=\"layoutright\"><a href=\"/\"><img src=\"/graphics/qbe.sas.topright.bretagne.png\" alt=\"Qbe SAS\" title=\"\" /></a>";
			break;
		case 4:
			echo "<div id=\"layoutright\"><a href=\"/\"><img src=\"/graphics/qbe.sas.topright.snow.png\" alt=\"Qbe SAS\" title=\"\" /></a>";
			break;
		default:
			echo "<div id=\"layoutright\"><a href=\"/\"><img src=\"/graphics/qbe.sas.topright.png\" alt=\"Qbe SAS\" title=\"\" /></a>";
			break;
		}
	}

	if ($sas_wantmenu)
	{

	global $qbe_app_menu;
	global $qbe_app_statusdaycounter;

	$status = 'login';
	if ( (!isset($userid)) || ($userid == "")) { $status = 'nologin'; }

	function sas_inc_php_checkgrouprereq($grouplist,$user)
	{
		$ok = false;
		$groups = split('\|',$grouplist);
	//	var_dump($groups);
		foreach ($groups as $thisgroup)
		{
			if (sas_ldap_isgroupmember($thisgroup,$user)) { $ok=true; }
		}
		return $ok;
	}

function sas_inc_php_rendersection($section,$status)
{	global $qbe_ssl,$user;

	if (isset($section['_section']['prereq'])) { if ($section['_section']['prereq'] != $status) { return; }}
	if (isset($section['_section']['prereq-group'])) { if (!sas_inc_php_checkgrouprereq($section['_section']['prereq-group'],$user)) { return; }}

	?><div class="menu_indent"><?php

	if (isset($section['_section']['title'])) { echo '<span class="menu_title">'.$section['_section']['title'].'</span>'; }
	if (isset($section['_section']['right'])) { echo '<span style="right: 2px; position: absolute;">'.$section['_section']['right'].'</span>'; }
	echo '<br/>';

	foreach ($section as $key => $entry)
	{
		$mode = 'link';
		if (isset($entry['mode'])) {$mode = $entry['mode']; }
		if (isset($entry['prereq'])) { if ($entry['prereq'] != $status) {continue;}}
		if (isset($entry['prereq-group'])) { if (!sas_inc_php_checkgrouprereq($entry['prereq-group'],$user)) { continue; }}
		if ($mode == 'link')
		{
			if (!isset($entry['link'])) { continue; }
			
			?><a href="<?=$entry['link']?>"><?=$entry['text']?></a><?php
	
			if ($qbe_ssl and isset($entry['add-ssl'])) { if ($entry['add-ssl'] == TRUE)
			{	?>
				(<a href="/modules/redir/ssl.php?url=<?=urlencode($entry['link'])?>">ssl</a>)
			<?php
			}}
			?><br /><?php
		}
		if ($mode == 'text')
		{
			?><?=$entry['text']?><br /><?php
		}
		if ($mode == 'code')
		{
			$entry['function']();
		}
	}

	?>
	</div>
		<?php
		qbe_web_makehr();
}

	if (isset($qbe_app_menu['_top']))
		sas_inc_php_rendersection($qbe_app_menu['_top'],$status);
	if (isset($qbe_app_menu['_user']))
		sas_inc_php_rendersection($qbe_app_menu['_user'],$status);
	foreach ($qbe_app_menu as $key => $section)
	{
		if ($key == '_top') { continue; }
		if ($key == '_user') { continue; }
		sas_inc_php_rendersection($section,$status);
	}

	echo '</div>';
	
 	}	// end want menu
	?>
	</body>
	</html>
	<!-- morgens frueh oder in der Nacht -->
	<!-- hat es in meinem Kopf gekracht. -->
	<!-- (c) copyright 2001, 2002, 2003, 2004 christian hofstaedtler -->
	
<?
	// sas_end() complete.
	if ($exit == true)
	{
		exit;
	}
}

function sas_perror($errtext)
{
	sas_pcode('error',$errtext);
}
function sas_pcode($code,$text)
{
	switch ($code)
	{
		case 'done':
		case 'success':
			$class = 'done';
			break;
		case 'error':
			$class = 'error';
			$text = 'Fehler: '.$text;
			break;
		case 'attention':
			$class = 'attention';
			$text = 'Achtung: '.$text;
			break;
		case 'info':
		default:
			$class = 'info';
			break;
	}
	echo '<br/><span class="'.$class.'">'.$text.'</span><br/><br/>';
}

function sas_filexs_makepath($show,$subdir='')
{
	global $userid;
	
	if ($show == '') {$show = 'own';}
	switch($show)
	{
	case 'own':
		$path = '/import/homes/'.$userid; 
		break;
	case 'group':
		$path = '/export/groups';
		break;
	case 'common':
		$path = '/export/share-alle';
		break;
	default:
		return '';
	}

	$path = $path . '/';
	if($subdir != '')
	{
		$subdir = $subdir .'/';
		$path = $path . $subdir;
	}
	return $path;
}
	function sas_filexs_varimport($var,$check = TRUE)
	{	global $$var;

		$tmp = isset($_REQUEST[$var]) ? $_REQUEST[$var] : '';
		if ($check)
		{
			if ($tmp != '')
			{
				$tmp = str_replace('..','',$tmp);
				$tmp = str_replace('//','',$tmp);
				if ($tmp[0] == '.') { exit; }
				if ($tmp[0] == '/') { exit; }
				if ($tmp[0] == '"') { exit; }
				if ($tmp[0] == "'") { exit; }
			}
		}
		$$var = $tmp;
	
	}


function qbe_modules_call($what)
{	global $qbe_modules;
	foreach ($qbe_modules as $moduleid => $module)
	{
		$func = 'qbe_module_'.$moduleid.'_' . $what;
		if (function_exists($func))
		{
			$func();
		}
	}
}

function qbe_restrict_access($group)
{	global $user;
	$groups = split('\|',$group);
	$ok = false;
	foreach($groups as $thisgroup)
	{
		if (sas_ldap_isgroupmember($thisgroup,$user))
		{
			$ok = true;
		}
	}
	if (!$ok)
	{
		sas_pcode('error',"Unbefugter Zugriff");
		sas_end();
		exit;
	}
}

function qbe_validate_mac($mac)
{
	if (strlen($mac) != 17) return FALSE;
	if (!eregi("^([0-9A-F])+(:[0-9A-F]{2}){5}$",$mac)) return FALSE;
	return TRUE;
}
function qbe_validate_computername($name)
{
	if (!preg_match("/^([A-Za-z]+)([A-Za-z0-9\-]*)$/i",$name))
		return FALSE;
	return TRUE;
}
function qbe_validate_ip($ip)
{
	if (!preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/i",$ip))
		return FALSE;
	return TRUE;
}

require($qbe_http_basepath.'/modules/defines.php');

function qbe_log_text($module,$severity,$text)
{
	openlog($module,LOG_NDELAY | LOG_PID,LOG_LOCAL7);
	syslog($severity, $text);
	closelog();
}

/* eof */

