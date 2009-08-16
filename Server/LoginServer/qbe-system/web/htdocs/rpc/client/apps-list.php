<?php
	require '/qbe/web/defines.php';
	header("Content-Type: text/xml");
	
	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
	echo "<applications>\n";

	$dir = opendir('../../applications/');
	while (false !== ($e = readdir($dir)))
	{
		if ( (substr($e,0,1) != '.') && (substr($e,-4) == '.xml') )
		{
			echo "<application><url>".'http://'.$qbe_http_server.'/applications/'.$e."</url><date>". date ("n/d/Y h:i:s A",filemtime('../../applications/'.$e)) ."</date><mode></mode></application>\n";
		}
	}
	closedir($dir);

	echo "</applications>\n";
	
