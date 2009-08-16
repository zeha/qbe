<?
include "../../sas.inc.php";
sas_start("Klasse l&ouml;schen","../../../","/admin/admin",1);
sas_showmenu();
qbe_restrict_access('useradm');

if (isset($_REQUEST['class'])) { $class = strtoupper($_REQUEST['class']); } else { $class = ""; }

if ($class == "")
{
	?>
	<br>
	<span class="error">Keine Klasse zum l&ouml;schen ausgew&auml;hlt.</span><br>
	<?
} else {

global $class, $sas_ldap_server, $sas_ldap_adminuser, $sas_ldap_adminpass, $PHP_SELF;

        $ds = ldap_connect($sas_ldap_server);
        ldap_bind( $ds , $sas_ldap_adminuser , $sas_ldap_adminpass );

	$results = ldap_search( $ds , $sas_ldap_base , '(ou='.$class.')' );
	$entries = ldap_get_entries( $ds, $results );
	
	?>
	<br/>
	L&ouml;sche Objekte...<br/>
	<br/>
	<?php

	$err = FALSE;
	$count = 0;
	foreach($entries as $entry)
	{
		if (!isset($entry['dn'])) { continue; }
		$count++;
		$dn = $entry['dn'];
		
		printf("%s...\n",$dn);
		$r = @ldap_delete( $ds , $dn );
		if ($r == TRUE) { echo 'ok'; } else { echo 'Fehler: '.ldap_error($ds); $err = TRUE; }
		echo "<br/>\n";

	}

	if (!$count)
	{
	?>
		<span class="error">Es wurden keine passenden Objekte gefunden.</span><br/>
		<br/>
	<?php
	}

	if ($err)
	{
	?><br/>
	Beim L&ouml;schen sind Fehler aufgetreten, tritt dies beim ersten Versuch auf, k&ouml;nnen die Fehler normal sein. Versuchen Sie in diesem Fall, die Klasse jetzt nochmal zu l&ouml;schen (F5 - Dokument neu laden).<br>
	Treten dann immer noch Fehler auf, kontaktieren Sie bitte den Technischen Support.<br/>
	<?php
	}
	

/*        if (@ldap_delete( $ldap , "cn=$class-$group,ou=$class,ou=Classes,ou=People,o=htlwrn,c=at" ))
	{
		echo "'$class-$group' wurde gel&ouml;scht!<br>";
	} else {
		echo "<span class=error>Fehler: ".ldap_error($ldap)."</span>";
	} */

	ldap_close($ds);
}

sas_end();
?>
