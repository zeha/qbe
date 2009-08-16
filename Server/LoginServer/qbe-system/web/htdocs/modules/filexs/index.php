<?php
	include("../../sas.inc.php");
	sas_start("Dateizugriff","../../","/modules/filexs",1);
	sas_showmenu();

	include("inc.php");
	$sizecount = 0;
	
	?><h2><?=$title?></h2>

	<form action="xfer-put" method=post enctype="multipart/form-data">
	<?
	qbe_web_makebox("uploadbox",100);
	function uploadbox()
	{	global $show,$subdir,$actionlink,$hideactions,$levelup;
	/*
	?>
			<input type=file name=uploadfile> <button type=submit>Upload</button>
			<input type=hidden name="show" value="<?=$show?>">
			<input type=hidden name="subdir" value="<?=$subdir?>">
			<input type=hidden name=actionlink value="<?=$actionlink?>">
			<input type=hidden name=hideactions value="<?=$hideactions?>">
			<br/>
		Maximale Dateigroesse: <?=get_cfg_var('upload_max_filesize')?>
		
	<?*/
		?>
		<a href="javascript:popupform('put-popup?actionlink=<?=urlencode($actionlink)?>;hideactions=<?=$hideactions?>;show=<?=$show?>;subdir=<?=$levelup?>');">Upload ...</a>
		<?
	} ?>
	</form>
		
	<? qbe_web_maketable(true); ?>
	<tr>
		<th style="background-color: white;"><img src="../../graphics/icon-dir-open.png" alt="[ROOT]" title="" height=16 width=16 class=icon></th>
		<th>Name</th>
		<th width=50>Gr&ouml;&szlig;e</th>
		<th>Besitzer</th>
		<th>Rechte</th>
		<th>Datum</th>
		<th></th>
	</tr>
	
	<?php
	if ($subdir != '')
	{
		$levelup = '/'.$subdir;
		$levelup = substr($levelup,0,strlen($levelup)-2);
		$levelup = substr($levelup,0,strrpos($levelup,'/'));
		if ($levelup != '') { if ($levelup[0] == '/') { $levelup = substr($levelup,1); } }
		
		qbe_web_maketr();
	?>
		<td style="background-color: white;"><img src="../../graphics/icon-dir-open.png" alt="[UP]" title="" class=icon></td>
		<td><a href="?actionlink=<?=urlencode($actionlink)?>;hideactions=<?=$hideactions?>;show=<?=$show?>;subdir=<?=$levelup?>">..</a></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>

	<?php
	}

	qbe_error_handler_off();

	$list = array();
	$directory = opendir($path);
	if ($directory != FALSE)
	{
		while ($file = readdir ($directory)) { 
			if ($file[0] != ".")
			{
				$list[] = $file;
			}
		}
		closedir($directory);
	} else {
		echo '</table>';
		sas_pcode('error','Die Berechtigungen erlauben es nicht, diese Seite auszuf&uuml;hren.');
		sas_end();
	}
	qbe_error_handler_on();

	sort($list);

	foreach ($list as $file)
	{
		$thisfile = $path.$file;
		$stat = stat($thisfile);

		$urlbase = ';hideactions='.$hideactions.';actionlink='.$actionlink.';show='.$show.';subdir='.urlencode($subdir);
		$urlfile = ';file='.urlencode($file);

		$icon = '../../graphics/icon-file.png';
		$link = 'xfer-get?'.$urlbase.$urlfile;
		if ($actionlink != '') { $link = $actionlink.$urlbase.$urlfile; }
		if (is_dir($thisfile))
		{
			$icon = '../../graphics/icon-dir.png';
			$link = '?'.$urlbase.$file;
		}

		$pw_user = posix_getpwuid($stat[4]);
		$pw_group = posix_getgrgid($stat[5]);

		$seeit = false;
		if ($pw_user['name'] == $userid)
		{	$seeit = true;	}
		if (sas_ldap_isgroupmember($pw_group['name'],$user))
		{	$seeit = true;	}
		if ( (fileperms($thisfile) & 0x0004) || (fileperms($thisfile) & 0x0002) || (fileperms($thisfile) & 0x0001) )
		{	$seeit = true;	}
		if (!$seeit) { continue; }
		
		$sizecount = $sizecount + filesize($thisfile);
		
		if ($link != '') { $link = '<a href="'.$link.'">'.$file.'</a>'; }
		else { $link = $file; }
		qbe_web_maketr();
		?>
			<td style="background-color: white;"><img src="<?=$icon?>" align=middle border=0 alt="[X]" title="" height=16 width=16 class=icon></td>
			<td><?=$link?></td>
			<td align=right><?=number_format(($stat[7]/1024)+1,0)?> KB</td>
			<td><?=$pw_user['name']?>.<?=$pw_group['name']?></td>
			<td><code><?=TranslatePerm(fileperms($thisfile))?></code></td>
			<td><?=strftime("%d.%m.%Y",$stat[10])?></td>
			<td><? if ($hideactions!='1') {?>
				<a href="act.php?asked=0;type=delete<?=$urlbase.$urlfile?>">L&ouml;schen</a>
				<a href="act.php?asked=0;type=rename<?=$urlbase.$urlfile?>">Umbenennen</a>
				<?php } ?>
			</td>
		</tr>
		<?
	}
	
	?>

	<tr>
		<td style="background-color: white;"></td>
		<td>Gesamt in diesem Verzeichnis:</td>
		<td align=right><?=number_format($sizecount/1024,0)+1?> KB</td>
	</tr>
	
	</table>
	<?php
	sas_end();

