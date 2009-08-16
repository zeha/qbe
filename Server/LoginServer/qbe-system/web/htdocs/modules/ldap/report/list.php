<?
	require("../../../sas.inc.php");
	sas_start("Berichte","../../../","/modules/core/report",2);
	sas_showmenu();

	$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
	$fields = isset($_REQUEST['fields']) ? $_REQUEST['fields'] : array('uid','displayname','ou','inetstatus','iphostnumber','traffic');

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	if ($filter == '') 
	{ 	?>
	
		<b>Beispielberichte:</b><br>
		<br>
		
		Alle Benutzer mit &uuml;berzogenem Internetlimit 
		<a href="<?=$_SERVER['PHP_SELF']?>?filter=(inetstatus=2)">suchen</a>
		<br>
		
		Alle Benutzer mit Projektfreischaltung
		<a href="<?=$_SERVER['PHP_SELF']?>?filter=(inetstatus=7)">suchen</a>
		<br>
<!--
		Jegliche Objekte mit IP-Adresse
		<a href="<?=$_SERVER['PHP_SELF']?>?filter=<?=urlencode("(&(objectClass=ipHost) ( !(iphostnumber=0.0.0.0)))")?>">suchen</a>
-->

		Alle Benutzer mit Internetlimit 100MB
		<a href="<?=$_SERVER['PHP_SELF']?>?filter=(traffic>=10000000)">suchen</a>
		<br>

		<?php 
		sas_end();
		exit;
	}

	?>Searching for <b><?=$filter?></b>.<br><?php

	$ds = ldap_connect($sas_ldap_server);
	ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);

	$search = ldap_search($ds,$sas_ldap_base,$filter,$fields);
	if ($search == TRUE)
	{

	$results = ldap_get_entries($ds,$search);

	?>Displaying <?=$results['count']?> results.<br><br>

	<? qbe_web_maketable(true); ?>
	<tr>
	<?
		foreach ($fields as $field)
		{	switch($field)
			{
				case 'uid': $field = 'UserID'; break;
				case 'displayname': $field = 'Name'; break;
				case 'ou': $field = 'Klasse'; break;
				case 'iphostnumber': $field = 'IP-Adresse'; break;
				case 'traffic': $field = 'Traffic'; break;
			}
			echo '<th>'.$field.'</th>'; 
		}
		?>
	</tr>
	<?php
	
	function displayfield($entry,$field,$index='')
	{
		if (isset($entry[$field]))
		{
			if ($index != '')
			{ echo $entry[$field][$index]; }
			else
			{ echo $entry[$field]; }
		}
	}

		foreach ($results as $key => $entry)
		{
			if (!is_array($entry)) { continue; }

			qbe_web_maketr();

			foreach ($fields as $field)
			{
				echo '<td>';
				if ($field == 'uid')
				{	
					echo '<a href="';
					if ($action == '')
					{
						echo '../display-user.php?popup='.$qbe_popup.'&uid=';
					} else {
						echo $action.'&popup='.$qbe_popup.'&uid=';
					}
					displayfield($entry,'uid','0');
					echo '">';

				}
				displayfield($entry,$field,'0');
				if ($field == 'uid') { echo '</a>'; }
			}

			echo '</tr>';
		}
	?>
	</table>

	<?php
	
	} else {
		echo 'Keine passenden Objekte gefunden.';
	}

	ldap_close($ds);

	sas_end();
?>
