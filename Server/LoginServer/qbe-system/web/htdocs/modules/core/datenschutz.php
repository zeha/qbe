<?
	include("../../sas.inc.php");
	sas_start("Datenschutz","../../","/modules/core",1);
	sas_showmenu();
?>

<b>DISCLAIMER:</b> Diese Seite dient nur zur Information, und kann/muss weder vollst&auml;ndig noch richtig sein. Die Informationen beziehen sich grunds&auml;tzlich nur auf Server-Systeme die der direkten Kontrolle des HTL Elektrotechnik Admin-Teams unterstehen.<br>
<br>

<h3>Ihre Daten, der Schutz, Ihre Pflichten und M&ouml;glichkeiten</h3>
Das System (inkl. ihrer statischen Daten und m&ouml;glicherweise vorhandenen Protokollinformationen) wird best-m&ouml;glichst (soweit sinnvoll und machbar) vor Zugriffen durch unberechtigten Personen gesch&uuml;tzt.
F&uuml;r administrative Zwecke ist es notwendig, Daten von Ihnen zu speichern. Die Zahl der Daten wird auf ein Minimum begrenzt, die Funktion des Systems muss aber sichergestellt sein. Welche Daten gespeichert werden, koennen Sie unter "Selbstauskunft" ablesen. 
Eine &Auml;nderung dieser Daten ist m&ouml;glich, wenden Sie sich dazu bitte schriftlich an den System-Administrator Dr. Karl Filz oder die Schulleitung.

Ausserdem werden Zugriffe auf diverse Ressourcen im und ausserhalb des HTL Netzwerkes protokolliert.<br>
<br>
Dies geschieht um:
<li>Geschwindigkeitsprobleme zu erkennen und beheben
<li>System (Software-)Probleme zu erkennen und beheben
<li>Mutwillige Besch&auml;digungen / Angriffe auf die Systeme zu erkennen, zu unterbinden und Massnahmen zu setzen. (=Beweismittel)
<br>
<br>
Einzelne Systeme k&ouml;nnen auch andere Tracking-Informationen speichern um Ihnen gewisse Dienste zur Verfügung zu stellen. Beispiele:
<br>
<li>Blackbox Admin-Dienst (Anmeldung)
<li>Blackbox Forum (letzter Zugriff)
<li>iLogin (Anmeldung)
<br>

<br>

<h3>Selbstauskunft</h3>


<?
$ldap = ldap_connect($sas_ldap_server);
ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

$l_list = ldap_read( $ldap , $user , "objectClass=posixAccount" );
if ($l_list)
{
	$l_entry = ldap_first_entry( $ldap , $l_list );
	$a = ldap_get_attributes( $ldap , $l_entry );


        ?>

<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border: 1px solid black;">

<tr><td width="30%">Name (cn):</td>
 <td width="70%"><?=$a['cn'][0]?></td></tr>

<tr><td>Anzeigename (displayName):</td>
 <td><?=$a['displayName'][0]?></td></tr>

<tr><td>Beschreibung (description):</td>
 <td><?=$a['description'][0]?></td></tr>

<tr><td>Klasse (ou):</td>
 <td><?=$a['ou'][0]?></td></tr>

<tr><td>uidNumber:</td>
 <td><?=$a['uidNumber'][0]?></td></tr>

<tr><td>sambaSID:</td>
 <td><?=$a['sambaSID'][0]?></td></tr>

<tr><td>Ihr PC:</td>
 <td>(<? if (isset($a['l'])) { echo $a['l'][0]; } else { echo "-"; }?>) 
 <? if (isset($a['macAddress'])) { echo $a['macAddress'][0]; } else { echo "-";}?>  /
 <? if (isset($a['ipHostNumber'])) { echo $a['ipHostNumber'][0]; } else {echo "-";}?> </td></tr>

<tr><td>DN:</td>
 <td><?=$user?></td></tr>

<tr><td>Aktueller PC:</td>
 <td><? if (isset($a['loggedonHost'])) { echo $a['loggedonHost'][0]; } else {echo"-";}?> / <? if (isset($a['loggedonMac'])) { echo $a['loggedonMac'][0]; }else{echo"-";} ?></td></tr>

<tr><td valign=top>Internetstatus (inetStatus):</td>
 <td valign=top><?if(isset($a['inetStatus'])){ echo $a['inetStatus'][0]; }?></td>
  </tr>

<tr><td valign=top>Letzte Aktivität (lastActivity):</td>
 <td valign=top>
  <?if(isset($a['lastActivity'])){echo $a['lastActivity'][0];}else{echo"-";}?>
 </td></tr>

<tr><td valign="top">Passwort:</td>
 <td valign=top>(Das Passwort wird irreversibel verschl&uuml;sselt abgespeichert.)</td>
 </tr>

<tr><td valign="top">Traffic:</td>
 <td valign="top"><?if(isset($a['traffic'])){echo $a['traffic'][0];}?></td></tr>

<tr><td valign="top">Diskspace:</td>
 <td valign="top">(wird hier nicht angezeigt, siehe Hauptseite)</td></tr>

<tr><td valign="top"></td>
 <td valign="top">
<?

if (isset($a['jpegPhoto'])) { ?><img src="tools/show_userphoto.php?uid=<?=rawurlencode($xuid)?>"><?}
?>
 </td></tr>
</table>

<?php } 

	sas_end();
?>

