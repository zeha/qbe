<?php
require("../../sas.inc.php");
sas_start('myAccounting',"../../","/modules/rfid/",1);

require("head.php");

?><h2>Absent</h2><?php
								
	$result = mysql_query ("Select * from entlehnunglog join deviceentlehnung on entlehnunglog.eid = deviceentlehnung.eid where entlehnunglog.tstmp_hy is NULL AND entlehnunglog.tstmp_bye <= DATE_ADD(NOW(),INTERVAL '00:59' MINUTE_SECOND) and uid = '" .$userid . "' ;");
		if (mysql_num_rows($result) == "0") { echo "Es sind zur Zeit keine Geräte auf Ihren Namen ausgeliehen"; }
		else {
			qbe_web_maketable(true);
			print ("<tr><th width=\"25%\">EntlehnungsID</th><th width=\"25%\">DeviceID</th><th width=\"25%\" >User</th><th width=\"25%\">Away Timestamp</th></tr>");
			while ($row = mysql_fetch_array($result)) {
				qbe_web_maketr();
				printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
					$row["EID"],
					$row["GID"],
					$row["UID"],
					$row["tstmp_bye"]
					);
													
					print("</tr>");
			}
print ("</table>");
		}									
			?>
	
	<h2>Reserved</h2>
	
	<?php
											
	$result = mysql_query ("SELECT  * FROM entlehnunglog JOIN deviceentlehnung ON entlehnunglog.eid = deviceentlehnung.eid WHERE entlehnunglog.tstmp_hy IS  NULL  AND entlehnunglog.tstmp_bye >= now(  ) and uid = '" .$userid . "' ;");
		if (mysql_num_rows($result) == "0") {  echo"Es sind keine Reservierung auf ihren Namen registriert";  }
		else {
			qbe_web_maketable(true);
			print ("<tr><th width=\"20%\">EntlehnungsID</th><th width=\"20%\">DeviceID</th><th width=\"20%\">User</th><th width=\"20%\">Away Timestamp</th><th></th></tr>");
			while ($row = mysql_fetch_array($result)) {
				qbe_web_maketr();
				printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
					$row["EID"],
					$row["GID"],
					$row["UID"],
					$row["tstmp_bye"]
					);
				 printf("<td><a href=\"delete.php?id=".$row["EID"]."\" onClick=\"return confirm('Wollen Sie wirklich löschen?')\"><img src=\"/graphics/icon-delete.png\" border=0></a></td></tr>");
			}
		print ("</table>");
		}									
						


 	echo '<meta http-equiv="refresh" content="600">';
	sas_end();
?>
