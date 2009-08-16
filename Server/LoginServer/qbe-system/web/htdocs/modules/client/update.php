<?
	
	require "version.php";

	error_reporting(0);
	#$basefile = "qbe".$curver.".exe";
	#	$filename = "/sas/web/htdocs/login/ilogin/" . $basefile;
	$filename = "files/QbeSASClient-Mini.exe";
	$filesize = filesize($filename);
	$ver = (isset($_GET['ver']) ? $_GET['ver'] : "0.0");
	if ($ver != $qbe_ilogin2_current)
	{
		header("Content-Disposition: attachment; filename=Qbe-SAS-Client-".$qbe_ilogin2_current.".exe");
		header("Content-Type: application/octet-stream");
		header("Content-Length: $filesize");
		
		$fp = fopen($filename, 'r');
		fpassthru($fp);
		fclose($fp);
		
	}
	else {
	//	echo "size: $filesize\n";
		header("HTTP/1.0 404 Not Found");
	}
