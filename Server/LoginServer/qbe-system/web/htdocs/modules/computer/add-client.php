<?
//
//

include "../../sas.inc.php";
sas_start("PC Eintragen","../../","/admin",1);
sas_showmenu();

qbe_restrict_access("teachers");

$whall = false;
if (sas_ldap_isgroupmember("halladm",$user))
{
	if (isset($_POST['wname'])){	$wname = strtoupper($_POST['wname']);	$whall = true; } else {	$wname = "";	}
}
if (isset($_POST['wip']))	{	$wip   = $_POST['wip'];				} else {	$wip = "";	}
if (isset($_POST['wmac']))	{	$wmac  = strtoupper($_POST['wmac']);		} else {	$wmac = "";	}

if ($wmac == "")
{
	sas_pcode('error','Invalid Parameters');
	sas_end();
}
	else
{

	$error = false;

	$sql = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
	$request = 'SELECT value+1 from sas.lastsaved WHERE type="uid-ntworkstation"';
	$result = mysql_query($request,$sql);
	$row = mysql_fetch_row($result);

	if ($row == FALSE)
	{ 	sas_perror("Konnte keine freie UID finden.");
		sas_end();
		exit;
	} else {
		$wuid = $row[0];
	}

	if (!$whall)
	{
		$request = 'SELECT value+1 from sas.lastsaved WHERE type="basename-client"';
		$result = mysql_query($request,$sql);
		$row = mysql_fetch_row($result);
		$widname = 0;
		$wname = "";
		if ($row == FALSE)
		{	sas_pcode('error',"Konnte keinen freien Namen finden.");
			sas_end();
		} else {
			$widname = $row[0];
		}
		$wname = "E-".$widname;
	} else {
		if (!qbe_validate_computername($wname))
		{
			sas_pcode('error',"Computername nicht g&uuml;ltig.");
			sas_end();
			exit;
		}
	}

	$wmac = str_replace(" ",":",$wmac);
	$wmac = str_replace("-",":",$wmac);
	if (!qbe_validate_mac($wmac))
	{	sas_perror("MAC Adresse im falschen Format!");
		sas_end();
		exit;
	}

	if ( (!qbe_validate_ip($wip)) || ($wip == '0.0.0.0') )
	{	sas_perror("IP Adresse im falschen Format!");
		sas_end();
		exit;
	}
	
	?><b>PC/Windows Name: </b><i><?=$wname?></i><br/>
	  <b>PC UID: </b><i><?=$wuid?></i><br/>
	  <b>Vorl&auml;ufige IP: </b><i><?
	  	if (intval($wip)>0) { echo $wip; } else { echo "(DHCP) 172.16.x.x"; }?></i><br/>
	  <b>MAC Adresse: </b><i><?=$wmac?></i><br/>
	  <b>Eigent&uuml;mer: </b><i><?=$user?></i><br/>
	<?

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	if ($b)
	{
		if (($wip != "") && ($wip != "0.0.0.0"))
		{
			$sr = ldap_search($ds,$sas_ldap_base,"(ipHostNumber=".$wip.")");
			$res = ldap_get_entries($ds,$sr);
			if ($res["count"] > 0)
			{
				$hostuser = $res[0]["uid"][0];
				if (isset($res[0]["owner"]))
				{       $hostuser = $res[0]["owner"][0]; }
				sas_perror("Diese IP wurde bereits fuer \"".sas_ldap_getusername($hostuser)."\" registriert.");
				sas_end();
			}
		}
		if ($wmac != '00:00:00:00:00:00')
		{
		$sr = ldap_search($ds,$sas_ldap_base,"(macAddress=".$wmac.")");
		$res = ldap_get_entries($ds,$sr);
		if ($res["count"] > 0)
		{
			$hostuser = $res[0]["uid"][0];
			if (isset($res[0]["owner"]))
			{	$hostuser = $res[0]["owner"][0]; }
			sas_perror("Diese MAC wurde bereits auf '".$hostuser."' registriert.");
			sas_end();
		}
		}

		$error = false;
		// can login, create user
		$info["objectClass"][0]="inetOrgPerson";
                $info["objectClass"][1]="posixAccount";
                $info["objectClass"][2]="shadowAccount";
                $info["objectClass"][3]="sambaSamAccount";
		$info["objectClass"][4]="qbeIpDevice";
		$info["objectClass"][5]="qbeOwnedObject";

		$info["owner"]=$user;
		
		$info["ipHostNumber"]=$wip;
		$info["macAddress"]=$wmac;

                $info["loginShell"]="/bin/nologin";
                $info["homeDirectory"]="/dev/null";

		$info["uid"]=$wname.'$';
		$info["sn"]=$info["uid"];
		$info["cn"]=$info["uid"];

		$info["uidNumber"] = $wuid;
		$info["gidNumber"] = "5002";
		
		$info["sambaSID"] = $sas_samba_domainsid . ($wuid*2+1000); 
		$info["sambaAcctFlags"] = "[WD         ]";
		$info["inetStatus"] = 1;

		// add data to directory
		$r=ldap_add($ds, "uid=" . $wname . "$, ou=hosts, ou=Administration, ".$sas_ldap_base, $info);
		if (! $r)
			$error = true;

		$errstr = ldap_error($ds);

		ldap_close($ds);

		if (!$error)
		{
			$request = 'UPDATE sas.lastsaved set value='.$wuid.' where type="uid-ntworkstation"';
	        	$result = mysql_query($request,$sql);
			echo mysql_error();

			if (!$whall)
			{
				$request = 'UPDATE sas.lastsaved set value='.$widname.' where type="basename-client"';
				$result = mysql_query($request,$sql);
				echo mysql_error();
			}
		}

		if (!$error)
		{
			qbe_log_text("qbe-appmodule-hosts-userworkstation",LOG_NOTICE,"Created new user workstation: $wname/$wuid/$wip/$wmac for $userid");
		}
	} else {
		$error = true; $errstr = "Logon Error";
	}
	

	if ($error)
	{
  		?><br/>
 		<span class="error">Ein Fehler ist aufgetreten.<br />
		<? if (isset($errstr)) { ?>Last Error: <?=$errstr?>.<?}?>
		</span>
		<?
  	} else {
		?><br/>
		<b>OK</b>, PC wurde abgespeichert. - <b>Bitte stellen Sie Ihren PC jetzt auf DHCP ein!</b>
		<?
	}

}

sas_end();
?>
