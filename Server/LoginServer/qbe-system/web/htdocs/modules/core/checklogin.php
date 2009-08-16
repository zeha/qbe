<?php
	require("../../sas.inc.php");

	sas_varImport('url');
	if ($url=='') {
		if ($qbe_ssl)
		{	// redir to ssl then.
			$url = 'https://'.$qbe_http_globalservername.'/'; 
		} else {
			$url = 'http://'.$qbe_http_globalservername.'/';
		}
	}

	if ($qbe_have_rpcclients)
	{
		$ip = sas_web_getclientip();
		if ($ip != '')
		{
			$rpcurl = 'http://'.$qbe_http_server.'/rpc/database-getuserfromip.php?ip='.$ip;

			$username = file($rpcurl,'r');
			$username = $username[0];
			// EDVO hack
			if (	(strlen($username)>4) &&
				(substr($username,0,1)=='d')
				)
				{ $username = ''; }

			if ($username == "*UNKNOWN*") { $username = ""; }
		} else { $username = ""; }
	} else { $username = ""; }
	
	if ($username == "")
	{
		if ($qbe_ssl)
		{
			header('Location: https://'.$qbe_http_globalservername.'/modules/core/login.php?go='.urlencode($url));
		} else {
			header('Location: /modules/core/login.php?go='.urlencode($url));
		}
		exit;
	}

	// we have to fill the session...

	session_start();
	$_SESSION['valid'] = 1;
	$_SESSION['user'] = sas_ldap_getdn($username);
	$_SESSION['pass'] = '';
	$_SESSION['dn'] = $_SESSION['user'];

        $_SESSION['ou'] = '';
        $_SESSION['abteilung'] = '';

        $ds = @ldap_connect($sas_ldap_server);
        if ($ds)
        {
		$r = ldap_bind($ds,$sas_ldap_machineuser,$sas_ldap_machinepass);
		$list = ldap_read ( $ds, $_SESSION['user'], "objectClass=*" );
		$entry = ldap_first_entry($ds, $list);
		$attrs = ldap_get_attributes($ds, $entry);
		$_SESSION['ou'] = $attrs['ou'][0];
		if (strstr($_SESSION['user'],'ou=People') != '')
		{
			$_SESSION['abteilung'] = strtoupper(substr(strstr($_SESSION['user'],'ou='),3,1));
		}
		ldap_close($ds);
	}

	$uid = sas_ldap_getuid($_SESSION['user']);
	$ou = $_SESSION['ou'];
	$abteilung = $_SESSION['abteilung'];																													
	session_write_close();
	header('Location: '.$url);
	
