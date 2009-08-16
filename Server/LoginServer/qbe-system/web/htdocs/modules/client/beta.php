<?
require "../../sas.inc.php";

sas_start("Qbe SAS Client Beta Stuff","../../","/modules/client/",0);
sas_showmenu();

?>
	<table class=borderoff>
	<tr>
	<td valign=top>
	<img src="../../graphics/qbox.png" alt="" class="qbox">
	</td>
	<td valign=top>

<?
	
	qbe_web_maketable(true,'style="min-width:200px"');
	echo '<tr><th colspan=4>Beta Stuff</th></tr>';
	
	$DIR = opendir("./files/beta/");
	while($FILE=readdir($DIR))
	{
		if (substr($FILE,0,1)!='.')
		{
			qbe_web_maketr();
		?>
		<td class="imagecol"><img src="/graphics/icon-file.png"/></td><td><a href="files/beta/<?=$FILE?>"><?=$FILE?></a></td><td><?=strftime("%d.%m.%Y %H:%m",filemtime('./files/beta/'.$FILE))?></td><td><?=sprintf("%0.0d",filesize('./files/beta/'.$FILE)/1024)?> kB</td></tr>
		<?
		}
	}
	closedir($DIR);
?>
	</table>
	
	
	</td>
	</tr>
	</table>

<?php
sas_end();
