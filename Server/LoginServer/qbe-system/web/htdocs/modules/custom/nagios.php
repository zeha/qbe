<?php
	//
	// CUSTOM FILE
	//
        if ($sas_sslstate == 'off')     $url = 'http';
	if ($sas_sslstate == 'on')      $url = 'https';
	$url = $url.'://view:view@qbe-auth.htlwrn.ac.at'
	?>
	<a href="http://view:view@status.htlwrn.ac.at/nagios/cgi-bin/statusmap.cgi?host=all&amp;layout=6"><img border="0" src="<?=$url?>/modules/nagios/map" name="netimg" id="netimg" alt="Das Netzwerk"></a>
						
