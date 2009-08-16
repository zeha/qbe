<?php
require("../../sas.inc.php");
sas_start('Actions logged',"../../","/modules/rfid/",1);
	
	require ("head.php");

	$log = mysql_query ("Select * from log");
//	$log = mysql_query ("Select * from entlehnunglog join deviceentlehnung on entlehnunglog.eid = deviceentlehnung.eid;");
	$ref = 3;
	/*if (mysql_num_rows($log) <= $ref) {		
		$betreff = "RFID Inventar Log of last 30 days";
		mail("christian@hofstaedtler.com", "Betreff", $betreff,
		"From: www-data@qbe-auth.htlwrn.ac.at \r\n");
	}*/
	mysql_query($log);
		qbe_web_maketable(true);
		print ("<tr><th>EID</th><th>GID</th><th>UID</th><th>tstmp_bye</th><th>tstmp_hy</th></tr>");
		while ($rows = mysql_fetch_array($log)) {
			
			qbe_web_maketr();
			printf ("<td width=\"150\">%s</td><td width=\"150\">%s</td><td width=\"150\">%s</td><td width=\"150\">%s</td><td width=\"150\">%s</td>",
				$rows["EID"],
				$rows["GID"],
				$rows["UID"],
				$rows["tstmp_bye"],
				$rows["tstmp_hy"]);
			
				}
			print "</table>";
?>	
<br />
<br />
<?php
	
	sas_pcode('attention','Die aufgelisteten Eintr&auml;ge werden mit den jeweils noch ausst&auml;ndigen Daten aktualisiert, also kein weiteres mal angereiht!!!');

	sas_end();
	
