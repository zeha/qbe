<?
	include "../../sas.inc.php";
	sas_start("Importer","../../","/admin/tools",2);
	sas_showmenu();

	sas_filexs_varimport('file');
	sas_filexs_varimport('show');
	sas_filexs_varimport('subdir');
	sas_varimport('type');

	$nextlink = $_SERVER['PHP_SELF'].'?file='.$file.'&show='.$show.'&subdir='.$subdir.'&';

	if ($file == '')
	{
		/*?>
			W&auml;hlen Sie eine <a href="../../modules/filexs/?hideactions=1&actionlink=<?=urlencode('/modules/import?type='.$type)?>">Datei</a> f&uuml;r den Import aus.<br>
			<br>
			<?php sas_makehelplink('import'); ?>	
			<br>
			<br>
			<br>
			Oder:<br>
			<a href="import_passwords">Nur Passw&ouml;rter setzen</a><br/>

		<?*/
		sas_pcode('error','Not this way.');

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
			
			$uidnum = '';
			if (isset($thisline[5]))
			{ $uidnum = $thisline[5]; }
			else 
			{
				$abt = substr($thisline[0],0,1);
				if ($abt == 'w') { $uidnum = 7; }

				$uidnum = $uidnum *10000 + substr($thisline[0],1,5);
			}
			
			$entries[] = array(
						'uid' => $thisline[0],
						'vorname' => $thisline[1],
						'nachname' => $thisline[2],
						'password' => $thisline[3],
						'ou' => $thisline[4],
						'uidNumber' => $uidnum
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
				'description' => 'Schueler',
				'ipHostNumber' => '0.0.0.0',
				'inetStatus' => '1001',
				'traffic' => '0',
				'loginShell' => '/bin/false',
				'gidNumber' => '513',
				'sn' => $entry['nachname'],
				'givenName' => $entry['vorname'],
				'displayName' => $entry['vorname'].' '.$entry['nachname'],
				'cn' => $entry['vorname'].' '.$entry['nachname'],
				'uid' => $entry['uid'],
				'mail' => $entry['uid'].'@htlwrn.ac.at',
				'homeDirectory' => '/import/homes/'.$entry['uid'],
				'dn' => 'uid='.$entry['uid'].',ou='.substr($entry['uid'],0,1).',ou=Students,ou=People,'.$sas_ldap_base,
				'ou' => $entry['ou'],
				'sambaAcctFlags' => '[U          ]',
				'uidNumber' => $entry['uidNumber'],
				'sambaSID' => $sas_samba_domainsid.'-'.(($entry['uidNumber']*2)+1000),
				'userPassword' => $entry['password']
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
					unset($info['dn']);
					printf("%s...\n",$dn);

					$r = @ldap_add($ds, $dn, $info);
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
