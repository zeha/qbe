<?php
require("../../sas.inc.php");
sas_start('Absent Devices',"../../","/modules/rfid/",1);

require("head.php");
								
		$result = mysql_query("Select * from entlehnunglog join deviceentlehnung on entlehnunglog.eid = deviceentlehnung.eid join devices on deviceentlehnung.gid = devices.gid where entlehnunglog.tstmp_hy is NULL;"); 
		if (mysql_num_rows($result) == "0") { echo "Es sind zur Zeit keine Geräte im Umlauf!"; }
		else {
			qbe_web_maketable(true);
			print ("<tr><th width=\"25%\">EntlehnungsID</th><th width=\"25%\">DeviceID</th><th width=\"25%\">User</th><th width=\"25%\">Away Timestamp</th></tr>");
			while ($row = mysql_fetch_array($result)) {	
							qbe_web_maketr();
							printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
							$row["EID"],
							$row["text"],
							$row["UID"],
							$row["tstmp_bye"]
							);						
			}
print ("</table>");
		}									
			?>
	
	<h2>Reserved</h2>
	
	<?php
											
	$result = mysql_query ("SELECT  * FROM entlehnunglog JOIN deviceentlehnung ON entlehnunglog.eid = deviceentlehnung.eid join devices on deviceentlehnung.gid = devices.gid WHERE entlehnunglog.tstmp_hy IS  NULL  AND entlehnunglog.tstmp_bye > now();");
		if (mysql_num_rows($result) == "0") {  echo"Es gibt zur Zeit keine Reservierungen";  }
		else {
			qbe_web_maketable(true);
			print ("<tr><th width=\"25%\">EntlehnungsID</th><th width=\"25%\">DeviceID</th><th width=\"25%\">User</th><th width=\"25%\">Away Timestamp</th></tr>");
			while ($row = mysql_fetch_array($result)) {
							qbe_web_maketr();
							printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
																		$row["EID"],
																		$row["text"],
																		$row["UID"],
																		$row["tstmp_bye"]
																		);
												
												
																}
		print ("</table>");
		}									
						

/*	
						?>
	<br/>
	<br/>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post"><button type=submit>Update</button></form>

<?php
 */
 	echo '<meta http-equiv="refresh" content="600">';
	sas_end();
?>
