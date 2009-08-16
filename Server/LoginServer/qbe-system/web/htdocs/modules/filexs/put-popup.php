<?php
	include("../../sas.inc.php");
	sas_start("Dateizugriff","../../","/modules/filexs",1,0);

	include("inc.php");
	$sizecount = 0;
		
	?>
	Ziel: <?=$path?><br/>
	
	<form action="xfer-put" method=post enctype="multipart/form-data">
	<br/>
	<table>
	<tr>
		<td>
			<input type=file name=uploadfile style="height: 1.6em;">
		</td>
	</tr>
	<tr>
	<td align=right>
			<button type=submit id=uploadbutton onClick="uploadbutton.enabled=false;">Upload</button>
	</td>
	</tr>
	</table>
			<input type=hidden name="show" value="<?=$show?>">
			<input type=hidden name="subdir" value="<?=$subdir?>">
			<input type=hidden name=actionlink value="<?=$actionlink?>">
			<input type=hidden name=hideactions value="<?=$hideactions?>">
			<br/>
		<b>Maximale Dateigroesse: <?=get_cfg_var('upload_max_filesize')?>B</b><br/>
		
	</form>
		
	<?php
	sas_end();

