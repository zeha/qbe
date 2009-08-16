<?php

	include("../../sas.inc.php");
	sas_start("Dateizugriff-Aktion","../../","/modules/filexs",1);
	sas_showmenu();

	include("inc.php");
	varImport('type');
	varImport('asked');
	$asked = intval($asked);

	echo '<h2>'.$title.'/'.$subdir.$file.'</h2>';
	
	if ($asked == 0) { ?><form method=post action="<?=$_SERVER['PHP_SELF']?>?asked=1&show=<?=$show?>&subdir=<?=urlencode($subdir)?>&file=<?=urlencode($file)?>&type=<?=$type?>"><? }

	
	if ($type == 'delete')
	{
		if ($asked == 0)
		{
			if (is_dir($path.$file))
			{?>
				Wollen Sie den Ordner wirklich l&ouml;schen?<br/>
			<?php }
			if (is_file($path.$file))
			{?>
				Wollen Sie die Datei wirklich l&ouml;schen?<br/>
			<?php }?>
			<br/>
			<button type=submit>Ja</button> <button type=reset onClick="history.go(-1)">Nein</button>
		<?
		}
		if ($asked == 1)
		{

			$shellfile = escapeshellarg($path.$file);
			$args = $shellfile;
			
			if (is_dir($path.$file))
			{
				`/qbe/sbin/qbe-filexs $userid - rmdir $args`;
				if (!is_dir($path.$file))
				{
					sas_perror("Konnte Ordner nicht l&ouml;schen.");
				} else {
					sas_pcode('success','Ordner gel&ouml;scht.');
				}
			} else {
				`/qbe/sbin/qbe-filexs $userid - unlink $args`;
				if (!is_file($path.$file))
				{
					sas_perror("Konnte Datei nicht l&ouml;schen.");
				} else {
					sas_pcode('success','Datei gel&ouml;scht.');
				}
			}

			makeBackLink();
		}
	}

	if ($type == 'rename')
	{
		if ($asked == 0)
		{
		?>
			Neuer Name: <input type=text name="newname" value="<?=$file?>" size=40><br/>
			<br/>
			<button type=submit>Umbenennen</button> <button type=reset onClick="history.go(-1)">Abbrechen</button>
		<?
		}
		if ($asked == 1)
		{
			varImport('newname');
			$shellfile = escapeshellarg($path.$file);
			$shellnewname = escapeshellarg($path.$newname);
	
			$args = $userid.' - rename '.$shellfile.' '.$shellnewname;
	
			`/qbe/sbin/qbe-filexs $args`;
	
			echo 'Datei wurde (falls Sie berechtigt sind) umbenannt.<br><br>';
			makeBackLink();
		}

	}
	
	if ($asked==0) { ?></form><? }
	
	sas_end();
?>

