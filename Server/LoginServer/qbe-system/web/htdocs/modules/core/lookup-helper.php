<?php
require"../../sas.inc.php";
sas_start("Lookup Helper","../../","/modules/core",0);
sas_showmenu();

	sas_varImport('uid');
	sas_varImport('popup');
	$field = substr($popup,2);
	if ($field == '')
		$field = 'uid';

	?>

	<script language="JavaScript">
		var inputField = this.opener.document.getElementById("<?=$field?>");
		inputField.value = '<?=$uid?>';
		window.close();
	</script>

	<?php

sas_end();
