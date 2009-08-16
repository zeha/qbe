<?
include "../../sas.inc.php";

sas_start("Systeminfo","../../","/modules/sysinfo",1);
sas_showmenu();
?>
	<a href="/modules/redir/outside?url=/modules/sysinfo/serverinfo/?template=aq&lng=de">Allgemeine Server-Info</a><br/>
	<br/>

	<b>Server-Uptime</b><br/> 
	&nbsp; <?=shell_exec("uptime")?><br/>

	<b>Server-Zeit</b><br/>
	&nbsp; <?=shell_exec("date")?><br/>
	<br/>
	
	<?
	$sysfail = "<span class=\"sysstate_crit\">";
	$sysyellow = "<span class=\"sysstate_fail\">";
	$sysok = "<span class=\"sysstate_pass\">";
	$sysend = "</span>";

	?>
	<table>
	<tr><td>
	
	<b>Services:</b><br>

	<?

	function printStateAndPids($daemon,$title)
	{	global $sysfail, $sysok, $sysend;
		$pids = str_replace("\n "," ",shell_exec("ps --format pid -C $daemon --no-headers"));
		if ($pids == "")
		{ echo "&nbsp; $title: $sysfail not running $sysend<br>"; }
		else
		{ echo "&nbsp; $title: $sysok running $sysend<br>"; /*$pids*/ }
	}

	printStateAndPids("qbe-sas-daemon","qbe-sas-daemon");
	printStateAndPids("cron","cron");
	printStateAndPids("ndsd","eDirectory (ndsd)");
	printStateAndPids("mysqld","MySQL");
	printStateAndPids("apache","Apache1.3");
	printStateAndPids("smbd","Samba");
	printStateAndPids("sshd","SSH (sshd)");
	printStateAndPids("dhcpd3","DHCP (dhcpd3)");
	printStateAndPids("heartbeat","heartbeat");
	printStateAndPids("apache2","subversion (Apache2)");
	printStateAndPids("vsftpd","FTP (vsftpd)");
	printStateAndPids("named","DNS (named)");

	?>
	</td><td>
	<b>Qbe AppServer Module:</b><br>
	<?
		foreach($qbe_modules as $key => $val)
		{ echo ' &nbsp; '.$key.'<br>'; }
	?>
	</td></tr></table>

	<br><br>
	<b>Storage:</b><br>
	<pre><?php
	echo strip_tags(`cat /proc/drbd`).'</pre>';
	
	?>
	<br><br>
	<b>Netzwerk:</b><br>
	<pre><?
	$iplist = `/sbin/ip addr list`;
	echo strip_tags($iplist);
	
	echo '</pre>';

	sas_end();
?>
