<?php
require("../../sas.inc.php");
sas_start('Ablauf der letzten 60 Minuten',"../../","/modules/rfid/",1);

	require("head.php");
?>

<h3>Ausgaben</h3>


										
<?php
	//$result = mysql_query ("SELECT * FROM entlehnunglog JOIN deviceentlehnung ON entlehnunglog.eid = deviceentlehnung.eid WHERE hour(tstmp_bye) >= hour( NOW( )-1) AND dayofmonth( tstmp_bye ) = dayofmonth(NOW( )) AND month( tstmp_bye ) = month( NOW() )  LIMIT 0 , 30 ;");
	$result = mysql_query ("SELECT * FROM entlehnunglog JOIN deviceentlehnung ON entlehnunglog.eid = deviceentlehnung.eid WHERE entlehnunglog.tstmp_bye >= date_sub(NOW(),interval '01:00' hour_minute) and entlehnunglog.tstmp_bye <= NOW() and  tstmp_hy is NULL;");

	if (mysql_num_rows($result) == "0") {  echo "Während der letzten 60 Minuten wurden keine Entlehnungen getätigt!";  }
	else {
		qbe_web_maketable(true);
		print ("<tr><th width=\"25%\">EntlehnungsID</th><th width=\"25%\">DeviceID</th><th width=\"25%\">User</th><th width=\"25%\">Away Timestamp</th></tr>");
		while ($row = mysql_fetch_array($result)) {
			qbe_web_maketr();
			printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
							$row["EID"],
							$row["GID"],
							$row["UID"],
							$row["tstmp_bye"]
					);
			
			}	
		print ("</table>");
	}
			?>

<h3>R&uuml;cknahmen</h3>

<?php
	
//	$result = mysql_query ("SELECT  * FROM entlehnunglog JOIN deviceentlehnung ON entlehnunglog.eid = deviceentlehnung.eid where tstmp_hy >= DATE_SUB(NOW(), INTERVAL '01:00' HOUR_MINUTE) AND MONTH(tstmp_hy)= MONTH(NOW()) AND DAYOFMONTH(tstmp_hy) = DAYOFMONTH(NOW())");
	$result = mysql_query ("SELECT * FROM entlehnunglog
					JOIN deviceentlehnung ON entlehnunglog.eid = deviceentlehnung.eid
					WHERE entlehnunglog.tstmp_hy > date_sub(NOW( ),interval '01:00' hour_minute);");

//	$result = mysql_query ("Select * from entlehnunglog join deviceentlhnung on entlehnunglog.eid = deviceentlehnung.eid where tstmp_hy-NOW() >= -3600 AND tstmp_hy-NOW()+3600 <= 0;");	
	if (mysql_num_rows($result) == "0") {  echo "Während der letzten 60 Minuten wurden keine Geräte zurück gebracht!";  }
	else {
		qbe_web_maketable(true);
		print ("<tr><th width=\"20%\">EntlehnungsID</th><th width=\"20%\">DeviceID</th><th width=\"20%\">User</th><th width=\"20%\">Away Timestamp</th><th width=\"20%\">Back Timestamp</th></tr>");
		while ($row = mysql_fetch_array($result)) {
			qbe_web_maketr();
			printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s     </td><td>%s</td>",
					$row["EID"],
					$row["GID"],
					$row["UID"],
					$row["tstmp_bye"],
					$row["tstmp_hy"]);
		}	
		print ("</table>");
	}	

	sas_end();

