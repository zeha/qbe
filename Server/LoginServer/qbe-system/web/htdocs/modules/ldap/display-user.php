<?
if (isset($_REQUEST['uid'])) { $xuid = $_REQUEST['uid']; } else { $xuid = ""; }

require "../../sas.inc.php";
sas_start("Benutzer Anzeigen","../../","/modules/core",1);
sas_showmenu();

qbe_restrict_access('useradm|passchange');


echo 'Rechte: ';
if (sas_ldap_isgroupmember('useradm',$user))
{
 echo 'Objekt ';
} 
if ( sas_check_group('useradm') || sas_check_group('userinetchange') )
{
 echo 'InetStatus ';
}
if ( sas_check_group('useradm') || sas_check_group('passchange') )
{
 echo 'Passwort';
}
echo '<br><br>';
if ($xuid != "")
{	
	$xuid = sas_ldap_getdn($xuid);
}

if ($xuid != "")
{	
        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

        $l_list = ldap_read( $ldap , $xuid , "objectClass=posixAccount" );
	if ($l_list)
	{
	        $l_entry = ldap_first_entry( $ldap , $l_list );
	        $a = ldap_get_attributes( $ldap , $l_entry );


qbe_web_maketable(true);
	?>

<!--table border="0" cellpadding="2" cellspacing="2" --> 
<form action="save-user" method=post> 
<input type=hidden value="<?=$xuid?>" name="userdn"> 

<?=qbe_web_maketr()?><th>Object ID:</th><td colspan=2><a href="display-object.php?dn=<?=$xuid?>"><?=$xuid?></a></td></tr>
<?=qbe_web_maketr()?><th>Name:</th><td colspan=2><?=$a['cn'][0]?> (<a href="<?=$qbe_report_templates['user-by-sn']?><?=$a['sn'][0]?>"><?=$a['sn'][0]?></a>)</td></tr>
<?=qbe_web_maketr()?><th>Anzeigename:</th><td colspan=2><? if (isset($a['displayName'][0])) { echo $a['displayName'][0]; }?></td></tr>
<?=qbe_web_maketr()?><th>Beschreibung:</th><td colspan=2><? if (isset($a['description'][0])) { echo $a['description'][0]; }?></td></tr>
<?=qbe_web_maketr()?><th>Klasse:</th><td colspan=2><a href="<?=$qbe_report_templates['class-list']?><?=$a['ou'][0]?>"><?=$a['ou'][0]?></a></td></tr>
<?=qbe_web_maketr()?><th>UID / SID:</th><td colspan=2><?=$a['uidNumber'][0]?> / <?=$a['sambaSID'][0]?></td></tr>
<?=qbe_web_maketr()?><th>Laptop:</th><td colspan=2><? if (isset($a['l'])) { echo $a['l'][0]; } else { echo "none";
}?>: <input name="mac" value="<? if (isset($a['macAddress'])) { echo $a['macAddress'][0]; }?>">, <? if
(isset($a['ipHostNumber'])) { echo '<input name=ip value="'.$a['ipHostNumber'][0].'">'; }?> </td></tr>
<?=qbe_web_maketr()?><th>Current Host:</th><td colspan=2><? if (isset($a['loggedonHost'])) { echo $a['loggedonHost'][0]; }?>, <? if (isset($a['loggedonMac'])) { echo $a['loggedonMac'][0]; } ?></td></tr>
<?=qbe_web_maketr()?><th>Internetstatus:</th>
 <td><?if(isset($a['inetStatus'])){?>
  <?=$a['inetStatus'][0]?><?}?> - <input type="text" size=3 value="<?if(isset($a['inetStatus'])){?><?=$a['inetStatus'][0]?><?} else {echo "0";}?>" name="inetstatus">
  </td><td>
  <small style="font-size: 7pt; line-height: 7pt;">0: Internet OK, 1: Generisch Gesperrt, 2: Limit ueberzogen,<br>
  	3: Fehlverhalten, 7: Projektfreischaltung, &gt; 1000: nicht aktiviert</small>
  </td></tr>
<?=qbe_web_maketr()?><th>Traffic:</th><td colspan=2><input name="traffic" value="<? if (isset($a['traffic'])) { echo $a['traffic'][0]; }?>"> Byte</td></tr>
<?=qbe_web_maketr()?><th>Times:</th>
    <td colspan=2><?if(isset($a['lastActivity'])){echo "LA: ".strftime("%T",$a['lastActivity'][0]);}?>
    </td></tr>
<?=qbe_web_maketr()?><th valign=top>JpegPhoto:</th><td colspan=2>
<? if (isset($a['jpegPhoto'])) { ?><img src="display-userphoto?uid=<?=rawurlencode($xuid)?>"><?}?></td></tr>
<?=qbe_web_maketr()?><th>Neues Passwort:</th><td colspan=2><input type=password name="newpass"></td></tr>
<?=qbe_web_maketr()?><th>Passwort bestätigen:</th><td colspan=2><input type=password name="newpass2"></td></tr>
<tr><td><i>Aktionen</i></td><td>
<table>
<tr>
<td><button type="submit" name="save">Speichern</button></td>
<? if (sas_ldap_isgroupmember('useradm',$user)) { ?></form><form action="save-user" method=post>
<input type=hidden value="<?=$xuid?>" name="userdn">
<td><button type="submit" name="delete">L&ouml;schen</button><? } else { echo '<td>'; } ?></td></tr>
</table>
</td></tr>
</form>
</table><br> 
<?
	include "../../includes/user-measure2.php";
	usermeasure($a['uid'][0],0);
?>
<br>
	<?
	}
	ldap_close($ldap);
}

sas_end();
?>
