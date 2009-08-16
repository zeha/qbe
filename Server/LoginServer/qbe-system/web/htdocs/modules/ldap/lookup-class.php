<?php
require"../../sas.inc.php";
sas_start("Benutzer verwalten","../../","/modules/core",1);
sas_showmenu();

qbe_restrict_access("useradm");
sas_varImport('prefill');

	?>
		<table class="borderoff">
		<tr><td></td><td><img src="/graphics/qbe.sas.topright.png" alt=""></td></tr>
		<tr>
		<form action="<?=$qbe_report_templates['lookup-class']?>" method=post>
		<input name="popup" value="<?=$qbe_popup?>" type=hidden />
		<?php if ($qbe_popup) { ?><input name="action" value="../../core/lookup-helper?" type=hidden><?php }?>
		<td> Klasse: </td>
		<td>
	<input name="value" value="<?=$prefill?>" maxlength="6" size="15"/>
	<button type=submit>Suchen</button> </td>
		</form>
		</tr>
		</table>
	<?php

sas_end();
