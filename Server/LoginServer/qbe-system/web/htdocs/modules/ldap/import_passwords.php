<?
	include "../../sas.inc.php";
	sas_start("Importer: Passwort setzen","../../","/admin/tools",2);
	sas_showmenu();

	sas_filexs_varimport('file');
	sas_filexs_varimport('show');
	sas_filexs_varimport('subdir');
	sas_varimport('type');

	$nextlink = $_SERVER['PHP_SELF'].'?file='.$file.'&show='.$show.'&subdir='.$subdir.'&';

	if ($file == '')
	{
		?>
			<a href="../../modules/filexs/?hideactions=1&actionlink=<?=urlencode('/modules/ldif/import_passwords?type='.$type)?>">Datei ausw&auml;len</a>
		<?

	} else {
		sas_varimport('stage');
		
		$sourcefile = sas_filexs_makepath($show,$subdir).$file;
		?>Benutze Datei "<?=$sourcefile?>"
		<br />
		<br />
		<?php

		$lines = file($sourcefile);
		$entries = array();
		foreach($lines as $line)
		{	
			$line = str_replace("\n",'',$line);
			$line = str_replace("\r",'',$line);
			$thisline = explode(';',$line);
			
			$entries[] = array(
						'uid' => $thisline[0],
						'password' => $thisline[3]
					);
		}

		if ($stage == '')
		{
			echo '<pre>';
			print_r($entries);
			?>
			</pre>
			<br /><a href="<?=$nextlink?>stage=convert">Weiter?</a><br/>
			<?php
		}
		
		$infos = array();
		foreach($entries as $entry)
		{
			$infos[] = array( 
				'dn' => 'uid='.$entry['uid'].',ou='.substr($entry['uid'],0,1).',ou=Students,ou=People,'.$sas_ldap_base,
				'userPassword' => $entry['password'],
				'inetStatus' => '1001'
				);
		}
		
		if ($stage == 'convert')
		{
			?><pre><?=print_r($infos)?></pre>
			<br /><a href="<?=$nextlink?>stage=import">Importieren?</a><br/>
			<?php
		}

		if ($stage == 'import')
		{	$err = FALSE;
		
			$ds = ldap_connect($sas_ldap_server);
			if (!$ds)
			{	$err = TRUE; }
			
			if (!$err)
			{
				if (!ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass)) $err = TRUE;
			}
		
			if (!$err)
			{
				foreach($infos as $info)
				{
					$dn = $info['dn'];
					printf("%s...\n",$info['dn']);
					unset($info['dn']);

					$r = ldap_mod_replace($ds, $dn, $info);
					if ($r == TRUE) { echo 'ok'; } else { echo 'Fehler: '.ldap_error($ds); }
					
					
					echo '<br/>';
				}
			}

			ldap_close($ds);
			
			if ($err)
			{
			?><span class="error">Systemfehler beim Import</span><br><br><?php
			}
		}
	}

	sas_end();
?>
