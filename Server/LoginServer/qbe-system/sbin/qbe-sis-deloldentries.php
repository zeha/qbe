#!/usr/bin/php4
<?php

require '/qbe/web/defines.php';
require '/qbe/web/htdocs/modules/sis/defines.php';

$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);

$termquery = "SELECT * from termin where unixdate < ".(time() - 604800)."";

$termresult = mysql_db_query($qbe_module_sis_dbname, $termquery);

while ($termrow = mysql_fetch_array($termresult)) {

	$deltermquery = "Delete from termin where id = '".(isset($termrow['id']) ? $termrow['id'] : '')."'";
	$deltermresult = mysql_db_query($db_name, $deltermquery);

}

$supquery = "Select * from supplierung where unixdate < ".(time() - 604800)."";
$supresult = mysql_db_query($qbe_module_sis_dbname, $supquery);

while ($suprow = mysql_fetch_array($supresult)) {

	$delsupquery = "Delete from supplierung where id = '".(isset($suprow['id']) ? $suprow['id'] : '')."'";
	$delsupresult = mysql_db_query($db_name, $delsupquery);

}


