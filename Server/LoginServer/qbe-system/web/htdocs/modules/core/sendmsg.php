<?php
	require("../../sas.inc.php");
	sas_start("Systemnachricht senden","../../","/modules/core",1);
	sas_showmenu();
	qbe_restrict_access("userinetchange|sysops");

	$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : '';
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';
	
	if ($uid != '') $dn = sas_ldap_getdn($uid); else $dn = '';
	if ( ($uid == '') || ($dn == '') ) { sas_perror("Der Benutzer existiert nicht."); sas_end(); exit; }

	if ( ($dn != '') && ($msg != '') )
	{
		$ds = ldap_connect($sas_ldap_server);
		ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
		$entry = ldap_read($ds,$dn,"(loggedonHost=*)");
		$entry = ldap_get_entries($ds,$entry);
		ldap_close($ds);

		$send_ip = @$entry[0]["loggedonhost"][0];

		if ($send_ip == '0.0.0.0') $send_ip = '';
		if ($send_ip == '') { sas_perror("Der Benutzer ist im Moment nicht angemeldet."); sas_end(); exit; }
		
		$msg = sas_ldap_getuid($user).': '.$msg;
		?>
		Sending message to: <code><?=$send_ip?></code><br/>
		Message text: <code><?=$msg?></code><br/>
		<?php

		$msgcode = urlencode($msg);
		$msgcode = str_replace('+','%20',$msgcode);
		$return = @file("http://".$send_ip.':7666/system/message?'.$msgcode);
		?>
		Results: <code><?=$return[0]?></code><br/>
		<?php

		if ($return[0][0] == 'O')
		{	// ok
		?>
			<span class="done">Nachricht gesendet.</span><br/>
		<?php
		} else {
			sas_perror("Der Nachrichtenversand ist fehlgeschlagen.");
		}
		
	} else {

		?><form action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<input type="hidden" name="uid" id="uid" value="<?=$uid?>">
		<?
		
		qbe_web_maketable(true);

		?><tr><th colspan=2>Nachricht an <?=$uid?> senden</th></tr>
		<?
		qbe_web_maketr();
		
		?>
		<td>	<label>Nachricht:</label></td>
		<td>	<textarea name="msg"></textarea></td>
		</tr>
		<?
		qbe_web_maketr();
		?>
		<td>	</td>
		<td>	
			<button type=submit>Senden</button>
		</td>
		</tr>

		<?php
		
		?></table></form><?
	}

	sas_end();

