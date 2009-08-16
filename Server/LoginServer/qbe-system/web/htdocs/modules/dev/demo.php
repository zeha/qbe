<?php
	require("../../sas.inc.php");
	sas_start("Developer Demo","../../","/modules/dev",0);
	sas_showmenu();

	function showdemo($call,$res_pre='',$res_post='')
	{
	?>
	<br>
	<b>Code:</b><br>
	<pre style="border: 1px solid black; margin-left: 20px;"><?=$call?></pre>
	<b>Resultat:</b><br>
	<div style="border: 1px solid black; margin-left: 20px;">
	<?php
		eval($res_pre);
		eval($call);
		eval($res_post);
		echo '</div>';
	}

	showdemo('sas_pcode(\'error\',"Fehlertext!!!");');
	showdemo('sas_pcode(\'attention\',"Hier ist was passiert.");');
	showdemo('sas_pcode(\'success\',"Ich habs geschafft!");');
	showdemo('sas_pcode(\'info\',"Ich weiss etwas!");');
	showdemo('qbe_web_makehr();');
	showdemo('qbe_web_makelookupform();','echo \'<label>UserID:</label> <input type="text" name="uid" id="uid">\';');
	showdemo('$dn = qbe_ldap_getobjectdn("cn=testarea");','','echo "Resultat: \$dn=\"".$dn."\"";');

	?>
	<br>
	Weiteres Interessantes:<br>
	<code>sas_end();</code> beendet den Seitenaufbau - Code nach sas_end() wird nicht mehr ausgef&uuml;hrt.<br>
	<br>
	<code>sas_varimport("adsf");</code> importiert die Variable $_REQUEST['adsf'] als $adsf in den global namespace.<br>
	<br>

	<?

	sas_end();

