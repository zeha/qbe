<?
	$sas_no_redirect = TRUE;	// qbesvc cant handle that correctly
require "../../sas.inc.php";
require_once('../../includes/user-measure.php');
require 'lib.php';

	qbe_error_handler_off();

	$debug = 0;
	if ($debug) error_reporting(15);

	$headers = getallheaders();
	
	$uid 	= isset($headers['iLogin-User']) ? $headers['iLogin-User'] : $_GET['user'] ;
#	$pass = base64_decode($headers['iLogin-PassCode']);
	$pass	= isset($headers['iLogin-Pass']) ? $headers['iLogin-Pass'] : $_GET['pass'] ;
	
	$version = isset($_GET['ver']) ? $_GET['ver'] : "";
	$useragent = $headers['User-Agent'];

	// get DN of user
	//$user = sas_ldap_getdn($uid);

	$sysip = sas_web_getclientip();			if ($debug==1) { echo "trying sysip: $sysip\n<br>"; }
	$sysmac = sas_web_getclientmac($sysip);		if ($debug==1) { echo "trying sysmac: $sysmac\n<br>"; }

	qbe_log_text("qbe-appmodule-client-login",LOG_NOTICE,"User Login Started from \"$sysip\" \"$sysmac\" \"$version\"");

	delLoginHost();

function checkRPC()
{
	global $sysip, $uid, $acceptOldVersion, $debug;

	if ($sysip == "") { return FALSE; }
	if (!isset($uid)) { return FALSE; }
	if ($uid == "") { return FALSE; }
							if ($debug==1) { 	echo "Checking RPC Client...";}
//	$rpcurl = "http://".$sysip.":7666/system/getinfo?type=username";
//	echo $rpcurl."<br>";
//	$rpc = fopen($rpcurl,"r");
/*	if (!$rpc)
	{	echo "RPC TIMEOUT\n";
		return FALSE;
	}
	socket_set_timeout($rpc,2);
//	$rpcuser = fread($rpc,7);
	fclose($rpc);
*/
	$rpc = popen('/usr/bin/perl /qbe/web/htdocs/admin/ilogin-rpc.pl '.escapeshellcmd($sysip),"r");
	$rpcuser = fread($rpc,7);
	fclose($rpc);

	$rpcuser = str_replace("\n","",$rpcuser);
	$rpcuser = str_replace("\r","",$rpcuser);

							if ($debug==1) { 	echo "RPC reports: $rpcuser\n"; }

	qbe_log_text("qbe-appmodule-client-login",LOG_NOTICE,"User Login: RPC reports: $rpcuser vs. $uid");

	if ($rpcuser == $uid)
		return TRUE;
		else
		return FALSE;
}

function checkactivation()
{
  global $sas_ldap_server, $user, $pass;

  $ds = ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = @ldap_bind($ds,$user,$pass);
        $list = ldap_read ( $ds, $user , "objectClass=*" );
        $entry = ldap_first_entry($ds, $list);
        $attrs = ldap_get_attributes($ds, $entry);
        $value = $attrs["inetStatus"][0];
        ldap_close($ds);
        if ($value > 999)
                return 1;
                else
                return 0;
  }
}
function setLoginHost()
{
  global $sas_ldap_server, $user, $pass, $sas_ldap_adminuser,$sas_ldap_adminpass;
  global $sysip, $sysmac;
  $ipisok = TRUE;
  $ds = ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
        $list = ldap_read ( $ds, $user , "objectClass=person" );
        $entry = ldap_first_entry($ds, $list);
	if ($entry != FALSE)
        	$attrs = ldap_get_attributes($ds, $entry);
	else
		$attry = array();
		
	if (!isset($attrs['inetStatus']))
	{
		$inetStatus = 0;
		$ipisok = FALSE;
	}
	else
		$inetStatus = $attrs['inetStatus'][0];

	if ($inetStatus == 3)
	{
		$ipisok = FALSE;
	}
		
	$ip = $sysip;
	$mac = $sysmac;
if ($sysip == "") { $mac = array(); $ip = array(); $ipisok = FALSE; }
	
	$attrs = array();

	$attrs["loggedonHost"] = $ip;
	$attrs["loggedonMac"] = $mac;
	$attrs["lastActivity"] = 0; // set to 0 instead of time() to indicate a fresh login
	// time();

	if ($ipisok == TRUE)
	{
		$r = ldap_modify($ds, $user, $attrs);
	}
        ldap_close($ds);

	return $ipisok;
  }
  return FALSE;

}

/*if ($version != "")
{
if (strcmp($version,$sas_need_ilogin) != 0)
{
	header("HTTP/1.0 402 Too Old");
	echo "OLD\r\n<br>";
	exit;
}
}*/

function doEdvoUglyHack($uid,$pass,$ip)
{
	global $user,$debug,$sas_ldap_server,$sas_ldap_adminuser,$sas_ldap_adminpass,$sas_ldap_base,$sas_samba_domainsid;

	// ok begin with: check the iprange!
//	if (strstr($ip,"10.5.9.") == $ip)
//	{
//		if ($debug) echo "edvo: ip range ok<br>";
//	} else {
//		if ($debug) echo "edvo: ip range wrong<br>";
//		return FALSE;
//	}

	// next, ask the EDVO ldap for the correct user DN
	$ds = ldap_connect("edvoldap.htlwrn.ac.at");
	ldap_bind($ds,"",""); // anonymous bind
	$results = ldap_search($ds,"ou=People,ou=Workstation,o=htlwrn,c=at", "(& (uid=".$uid.") (objectClass=posixAccount))", array("cn","uidNumber","uid"));
	$info = ldap_get_entries($ds, $results);
	if ($info["count"] != 1)
	{
		// something unsusal happened
		// *DONT* continue
		return FALSE;
	}

	$user = $info[0]["dn"];
	if ($user == "") { return FALSE; }
	if ($debug) echo "edvo: ".$user.'<br>';
	
	$user_name = $info[0]['cn'][0];
	$user_id = $info[0]['uidnumber'][0];
	$user_obj = $info[0]['uid'][0];
	
	if (@ldap_bind($ds,$user,$pass) == FALSE)
	{	// wrong password
		return FALSE;
	}

	ldap_close($ds);	// log out from edvo

	// OK now we know a lot of things
	if ($debug) echo 'edvo: creating user '.$user_obj.' with id '.$user_id.' and name '.$user_name.'<br>';

	// create a new user object, *without* password, inetstatus=0
	$ldap = ldap_connect($sas_ldap_server);
	ldap_bind($ldap,$sas_ldap_adminuser,$sas_ldap_adminpass);
	
	// well, search first if the user already exists..
	$results = ldap_search($ldap,"ou=d,ou=Students,ou=People,".$sas_ldap_base, "(& (uid=".$uid.") (objectClass=posixAccount))", array("cn"));
	$info = ldap_get_entries($ldap, $results);
	if ($info["count"] == 1)
	{
		if ($debug) echo 'edvo: user already there.<br>';
		ldap_close($ldap);
		return TRUE;
	}
	
/*	$new['objectClass'][0] = 'posixAccount';

	$new['uid'] = $user_id;
	$new['cn'] = $new['displayName'] = $user_name;
	$new['inetStatus'] = 7;
	$new['ou'] = 'EDVO';
*/

$new = array(
'objectClass' => array (
'inetOrgPerson',
'posixAccount',
'shadowAccount',
'sambaSamAccount',
'organizationalPerson',
'person',
'qbeIpDevice',
'ndsLoginProperties',
'top'),
'description' => 'EDVO-Schueler',
'ipHostNumber' => '0.0.0.0',
'inetStatus' => '7',
'traffic' => '0',
'loginShell' => '/sbin/nologin',
'gidNumber' => '201',
'sn' => $user_name,
'displayName' => $user_name,
'cn' => $user_name,
'uid' => $user_obj,
'homeDirectory' => '/dev/null',
//'dn' => 'uid='.$user_obj.',ou=d,ou=Students,ou=People,'.$sas_ldap_base,
'ou' => 'EDVO',
'sambaAcctFlags' => '[U  ]',
'uidNumber' => $user_id,
'sambaSID' => $sas_samba_domainsid.'-'.(($user_id*2)+1000)
);

	ldap_add($ldap,'uid='.$user_obj.',ou=d,ou=Students,ou=People,'.$sas_ldap_base,$new);

	ldap_close($ldap);

	return TRUE;
}

function logit($user,$ip,$mac,$version,$success = 'fail')
{
	qbe_log_text("qbe-appmodule-client-login",LOG_NOTICE,"User Login: \"$success\" \"$user\" \"$ip\" \"$mac\" \"$version\"");
}


	/*
	 *
	 * Login process starting here:
	 *
	 */

	$CODE = '';
	$STATUS = TRUE;

	// check empty username, password or id
	if ($uid == "") { $STATUS = FALSE; $CODE = '-empty'; }
	if ($pass == "") { $STATUS = FALSE; $CODE = '-empty'; }
	if ($sysip == "") { $STATUS = FALSE; $CODE = '-empty'; }
	if ($sysmac == "") { $STATUS = FALSE; $CODE = '-empty'; }
	if ($STATUS == TRUE) { if (strstr($useragent,"Qbe") == "") { $STATUS = FALSE; $CODE = '-client'; } }
	
	// Check if Client comes from an external IP
	if ($STATUS == TRUE) { if (substr($sysip,0,3) != "10.") { $STATUS = FALSE; $CODE = '-wrongnet'; } }
	
	if ($STATUS == TRUE)
	{
		if (checkRPC() == FALSE) { $STATUS = FALSE; $CODE = '-rpc'; }
	//	if ($STATUS == TRUE) 
		{
			// ok, now we check the password
			// but we do some ugly hack instead - check for EDVO users first
			if ( (strlen($uid)>2) && (substr($uid,0,1)=="d") )
			{
				// do the edvo thing
				if (!doEdvoUglyHack($uid,$pass,$sysip))
				{ $STATUS = FALSE; $CODE = '-edvo1'; } 
				else 
				{	if ($debug) echo 'edvo: hackfunc said ok<br>';
					$user = sas_ldap_getdn($uid);
					if ($debug) echo 'edvo: our user dn: '.$user.'<br>';
					// recheck RPC and then we're ok.
					//if (checkRPC() == TRUE)
					{ $STATUS = TRUE; }
					//else
					//{ $STATUS = FALSE; }
				}
			} else {
				if ($STATUS == TRUE)
				{
					// do the normal eDirectory things
					$user = sas_ldap_getdn($uid);
					if (!sas_ldap_checkpassword($user,$pass)) 
					{ $STATUS = FALSE; $CODE = '-pass'; } 
				}
			}
		}
	}

	if ($STATUS == TRUE) { if (setLoginHost() != TRUE) { $CODE = '-auth'; $STATUS = FALSE; } }
	if ($STATUS == TRUE) { if (checkactivation()) { $CODE = '-activation'; $STATUS = FALSE; } }

	if ($STATUS == FALSE)
	{
		logit($uid,$sysip,$sysmac,$useragent,'fail'.$CODE);
		if ($CODE == '-pass')
		{
			header("HTTP/1.0 403 Forbidden");
		} else {
			if ($CODE == '-activation')
			{
				header("HTTP/1.0 401 Activation Required");
			} else {
				header("HTTP/1.0 412 Precondition failed");
			}
		}
		echo "FAIL ".$CODE."\r\n<br>";

	}
	else 
	{
		// tell proxy to recreate config
		system("touch /qbe/status/acl/update");

		logit($uid,$sysip,$sysmac,$useragent,'ok');

		$ds = ldap_connect($sas_ldap_server);
		ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);

		$list = ldap_read ( $ds, $user , "objectClass=*" );
		$entry = ldap_first_entry($ds, $list);
		$attrs = ldap_get_attributes($ds, $entry);

		$stats_traffic = $attrs['traffic'][0];
		$stats_disk = getdiskspace($uid);
		$inetstate = $attrs['inetStatus'][0];

#		if ($inetstate == 7) { writeacl($sysip,0); $inetstate = 0; }
#		if (($inetstate == 1) && (strcmp($ip,"10.5.9.") == 0)) { $inetstate = 0; writeacl($sysip,0); }

		ldap_close($ds);

		header("HTTP/1.0 200 Ok");
		header("iLogin-User-State: ".$inetstate);
		header("iLogin-Stats-Traffic: ".($stats_traffic/1000/1000/150*100));
		header("iLogin-Stats-Disk: ".($stats_disk/20*100));
		header("iLogin-Timestamp: ".time());
#		echo "PASS\r\niLogin-User-State: ".$inetstate."\r\niLogin-Stats-Traffic: ".($stats_traffic/1000/1000/150*100)."\r\niLogin-Stats-Disk: ".($stats_disk/20*100)."<br>";
	}
