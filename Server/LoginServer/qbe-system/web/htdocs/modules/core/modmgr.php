<?php
	require("../../sas.inc.php");
	sas_start("Modulverwaltung","../../","/modules/core",1);
	sas_showmenu();

	?><b>Geladene Module:</b><br/>
	<br/>
	<?php
	qbe_web_maketable(true);

	?>
	<tr>
		<th></th>
		<th>Module</th>
		<th>Version</th>
		<th>Description</th>
		<th>Copyright</th>
	</tr>
	<?php

	foreach ($qbe_modules as $moduleid => $module)
	{
		qbe_web_maketr();
		?>
		<td>
		<?php
		if (sas_check_group('sysops')) {
			echo '<input type=checkbox name="load-'.$moduleid.'" value=1 checked>';
		}
		?>
		</td>
		<td>
		<?=$moduleid?>
		</td>
		<?
		if (is_array($module))
		{
			if (isset($module['version']))
			{
				echo '<td>'.$module['version'].'</td>';
			} else {
				echo '<td></td>';
			}	
			?>
			<td>
			<?=$module['desc']?>
			</td>
			<td>
			<?=$module['copyright']?>
			</td>
			<?php
		} else {
			?>
			<td colspan=3 align=center>
			keine weiteren Informationen
			</td>
			<?php
		}
		?>
		</tr>
		<?php
		
	}
	echo '</table>';

	if (sas_check_group('sysops'))
	{
		echo '<br>well we should do some checkbox on/off stuff here. TODO.';
	}

	sas_end();

