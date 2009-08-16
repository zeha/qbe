<?php
require"../../sas.inc.php";
sas_start("Lookup wrapper","../../","/modules/core",0);
	
	sas_varImport('subject');
	sas_varImport('prefill');
	switch ($subject)
	{
		case 'user':
		case 'group':
		case 'class':
		case 'object':
			$provider = 'user';
			$provider = $qbe_providers[$provider];
			break;
		default:
			$subject = '';
			$provider = '';
	}

	if ($provider == '')
	{
		sas_perror("Konnte passenden Provider nicht ermitteln.");
		exit;
	}

	?>
	<meta http-equiv="refresh" content="0; url=/modules/<?=$provider?>/lookup-<?=$subject?>?popup=<?=$qbe_popup?>&prefill=<?=$prefill?>">
	<?php

sas_end();
