<?php
include "../../sas.inc.php";
sas_start("System Changelog","../../","/modules/changelog",1,0);
qbe_restrict_access('sysops');

?>
<html>
<pre>
<?php
	$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);
	$query = mysql_query("SELECT * FROM changelog.changelog ORDER BY date DESC");
	echo mysql_error();
	
	$old_date = '';
	$old_who = '';

	?>
\begin{tabular}{l|l}
<?php

	while ($row = mysql_fetch_array($query))
	{
?><?=$row['date']?> & <?=$row['descr']?> \\
<?php
	}

	?>
\end{tabular}
	<?php

	mysql_close($db);

sas_end();
