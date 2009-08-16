<?php

$termquery = "Select * from termin where unixdate < ".(time() - 604800)."";

$termresult = mysql_db_query($db_name, $termquery) or die(mysql_error());

while ($row = mysql_fetch_array($termresult)) {

	$deltermquery = "Delete from termin where id = '".$row['id']."'";
	$deltermresult = mysql_db_query($db_name, $deltermquery) or die(mysql_error());

}

$supquery = "Select * from supplierung where unixdate < ".(time() - 604800)."";
$supresult = mysql_db_query($db_name, $supquery) or die(mysql_error());

while ($row = mysql_fetch_array($supresult)) {

	$delsupquery = "Delete * from supplierung where id = '".$row['id']."'";
	$delsupresult = mysql_db_query($db_name, $delsupquery) or die(mysql_error());

}

?>



