<?php

	include("../../sas.inc.php");
	sas_start("Dateizugriff","../../","/modules/filexs",1,0);

	include("inc.php");

	// parameter checks
	$thisfile = $path.$file;
		
	if (!is_file($thisfile)) { exit; }

	$xf = escapeshellarg($thisfile);
	$type = `file -ib $xf`;
	header('Content-Type: '.$type);
	header("Content-Disposition: attachment; filename=".basename($file));

	error_reporting(0);

	//$readfile($thisfile);
	passthru("/qbe/sbin/qbe-filexs ".$userid." - fileget ".escapeshellarg($thisfile));
	
	

