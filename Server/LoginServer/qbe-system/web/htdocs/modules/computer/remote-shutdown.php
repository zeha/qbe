<?php
	require('../../sas.inc.php');
	sas_start("PC Remote Shutdown","../../","/modules/computer",1);
	qbe_restrict_access("userinetchange");
	
	sas_varimport('ip');
	sas_varimport('action');
	if ($ip != "")
	{	$a = 'shutdown';
		if ($action == 'restart')
		{	$a = 'restart'; }
		
		$f = @fopen('http://'.$ip.':7666/system/'.$a.'?Shut%20down%20from%20web%20interface.','r');
		if ($f == NULL)
		{
			sas_pcode('attention','Verbindung zu '.$ip.' fehlgeschlagen.');
		} else {
			fclose($f);
		}
	}

	?>
		<form method=post>
		<label>IP Adresse:</label> <input type=text name="ip" value="" /><br/>
		<input type=checkbox name="action" value="restart"> PC neu starten<br/>
		<button type=submit>Okay!</button>
		</form>
	<?php

	sas_end();
