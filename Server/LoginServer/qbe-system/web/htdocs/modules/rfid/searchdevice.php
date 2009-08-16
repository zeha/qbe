<?php
require("../../sas.inc.php");
sas_start('Device Search',"../../","/modules/rfid/",1);

require "head.php";
?>
											

<table>
<tr>
		<td width="100%" align="center" colspan="10"><br>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
				<tr><td colspan="2" height="1" bgcolor="#000000"></td></tr>
				<tr align="center">
					<td width="25%" style="margin-left:100px; margin-top:20px;"><br>
					<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	
					<select name="dev" style="width:150px">
						<?php    
								$device_select = @mysql_query ("Select GID,text from devices");
								print "<option value=\"\">choose device</option>";
								while ($freedevice = mysql_fetch_array($device_select)) {
								print ("<option value=\"" . $freedevice["GID"] . "\">" . $freedevice["text"] . "</option>");
									}								
						 ?></select><br><br>
										
				<td valign="top" width="25%"><br>
						<button type="submit">Suchen</button>
					</form>
				</td></tr>
			</table>
		</td>
	</tr>
	<tr><td colspan=20><hr noshade size="1" color="black"></tr>
	</table>

<?php
	sas_varimport("dev");
	
	$search = @mysql_query("Select * from devices where gid = '".$dev."'");
	while($result = mysql_fetch_array($search))
		{
		
		qbe_web_maketable(true);
		qbe_web_maketr();
		print("<td colspan=\"6\"><h2>Search Results for ".$result['GID']."</h2></td></tr>");
		qbe_web_maketr();
		print("<td>GID</td><td>Inventar Nummer</td><td>Raum Nummer</td><td>Kasten Nummer</td><td>Platz Nummer</td><td>Bezeichnung</td></tr>");
		qbe_web_maketr();
		print("<td>".$result['GID']."</td><td>".$result['invnr']."</td><td>".$result['rnr']."</td><td>".$result['knr']."</td><td>".$result['pnr']."</td><td>".$result['text']."</td>");
		
		print ("</tr></table>");
		}
	$isgone = @mysql_query("Select * from entlehnunglog join deviceentlehnung on entlehnunglog.eid = deviceentlehnung.eid where gid = '" .$dev . "' and tstmp_hy is null and tstmp_bye >= date_sub(now(),interval'01:00' hour_minute);");
	if (mysql_num_rows($isgone) > 0){print("verdammt");} 

	sas_end();
?>
