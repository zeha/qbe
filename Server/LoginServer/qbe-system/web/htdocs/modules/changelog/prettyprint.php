<?php
include "../../sas.inc.php";
sas_start("System Changelog","../../","/modules/changelog",1,0);
qbe_restrict_access('sysops');

?>
<html>
<style>
	td,th { border: 1px solid black; }
</style>
<body>
<?php
	$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
	$query = mysql_query("SELECT * FROM changelog.changelog ORDER BY date DESC");
	echo mysql_error();
	
	$old_date = '';
	$old_who = '';

	?>
	<table cellpadding=1 cellspacing=1>
	<tr><th>Datum</th><th>Person</th><th>T&auml;tigkeit</th></tr>
	<?php

	while ($row = mysql_fetch_array($query))
	{?>
		<tr><td><?=$row['date']?></td><td><?=$row['who']?></td><td><?=$row['descr']?></td></tr>
	<?php
	}

	?>
	</table>
	<?php

	mysql_close($db);

sas_end();
?>
