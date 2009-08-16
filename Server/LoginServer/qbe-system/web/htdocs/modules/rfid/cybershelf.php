<?php
require("../../sas.inc.php");
sas_start('Reservierung',"../../","/modules/rfid/",1);

require "head.php";
?>
											

<table>
<tr>
		<td width="100%" align="center" colspan="10"><br>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
				<tr><td colspan="2" height="1" bgcolor="#000000"></td></tr>
				<tr align="center">
					<td width="25%" style="margin-left:100px; margin-top:20px;"><br>
					<script language="JavaScript">
					function confirmSubmit()
					{
					return confirm("Reservierung abschicken?");
					}
					</script>
					<form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return confirmSubmit()" >
	
					<select name="dev" style="width:150px">
						<?php    
								$device_select = mysql_query ("Select GID,text from devices;");
								print "<option value=\"\">choose device</option>";
								while ($freedevice = mysql_fetch_array($device_select)) {
								print ("<option value=\"" . $freedevice["GID"] . "\">" . $freedevice["text"] . "</option>");
									}								
						 ?></select><br><br>
					
					<select name="timedrop" style="width:150px">
						<option value="">choose time</option>
						<option value="7:50">7:50 (1 Stunde)</option>
						<option value="8:40">8:40 (2 Stunde)</option>
						<option value="9:30">9:30 (3 Stunde)</option>
						<option value="10:25">10:25 (4 Stunde)</option>
						<option value="11:25">11:25 (5 Stunde)</option>
						<option value="12:20">12:20 (6 Stunde)</option>
						<option value="13:15">13:15 (7 Stunde)</option>
						<option value="14:15">14:15 (8 Stunde)</option>
						<option value="15:10">15:10 (9 Stunde)</option>
						<option value="16:05">16:05 (10 Stunde)</option>
						<option value="17:00">17:00 (11 Stunde)</option></select> <br><br>
										
					<select name="datedrop" style="width:150px">
						<option value="">choose date</option>
						<?php
							$now = time();
							for ($a = 0; $a <= 8; $a++)
							{
								$date = $now + (86400 * $a);

								if (date("D",$date) != "Sun") 
								{
							
								print "<option value=\"" . date("Y-m-d",$date) . "\">" . date("d-m-Y",$date) . "</option>";
								
								}
							}
						?>
					</select>
					<br><br>
				</td>
				<td valign="top" width="25%"><br>
					<input type="text" name="username" value="<?=$userid?>"/><br>Username<br><br>
						<button type="submit">Reservierung abschicken</button>
						<input type="hidden" name="action" value ="save">
					</form>
				</td></tr>
			</table>
		</td>
	</tr>
	<tr><td colspan=20><hr noshade size="1" color="black"></tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" >
		<tr >
			<td colspan="20"><br></td>
		</tr>
						
		<tr>
			<td>
			<?php
			
				sas_varimport('datedrop');
			        sas_varimport('dev');
			       	sas_varimport('timedrop');
			        sas_varimport('username');
				sas_varimport('action');

				$device = $dev;// isset($_REQUEST['dev']) ? $_REQUEST['dev'] : '';
				$date = $datedrop;// isset($_REQUEST['datedrop']) ? $_REQUEST['datedrop'] : '';
				$time = $timedrop;// isset($_REQUEST['timedrop']) ? $_REQUEST['timedrop'] : '';
				//$username =  isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
				//$setvars = isset ($_REQUEST['setvars']) ? $_REQUEST['setvars'] : '';
				
			if ($action == 'save')	{
		
				if ($date == '' || $device == '' || $time == '')
					{														                                                             sas_pcode('attention','!! Keine vollständige Eingabe | Bitte wiederholen Sie Ihre Anfrage !!');
					}
			       else {
																																													      
/*									
					if ($username && $device && !$date && !$time)  {	
					// Mal checken obd as Gerät weg ist, bzw. ob es reserviert ist!
												
					$check_gid = @mysql_query ("Select gid from deviceentlehnung");
					mysql_query($check_gid);
					$write = "1";
					 while ($hm = mysql_fetch_array($check_gid)) {
						if ($hm["gid"] == $device) {
						$write="0";
						}//if ende
						else  {
						$write="1";
						}
					} //while ende
					if ($write == "0") echo "Dieses Gerät ist bereits ausgeborgt";
					if ($write == "1") {
					// AUSBORGEN -> ZEIT UND DATUM WERDEN FÜR DEN AUSBORGEZEITPUNKT
						$query = "INSERT INTO entlehnunglog ( EID , UID , tstmp_bye , tstmp_hy ) VALUES (''," . $username . ", NOW() , NULL();";
						mysql_query ($query);
						echo $username;
						$result = mysql_query ("SELECT EID FROM entlehnunglog order by EID desc LIMIT 0 , 1 ;");
						$row = mysql_fetch_array($result);					
						$insert = "INSERT INTO deviceentlehnung (EID,GID) Values (" . $row["EID"] . "," . $device . ");";
						mysql_query ($insert);
														
						echo "Anfrage gesendet\t";
						print "Sie " . $username . " haben sich gerade das Gerät " . $device . " ausgeborgt";
														
					}//end if write
												
				}//if isset ende
*/											
				// RESERVIEREN -> ZEIT UND DATUM WERDEN EINGEGEBEN
				//if ($username && $device && $date && $time) 
				//{
												
					// mal checken ob das ding zu dem zeitpunkt da is!!
													
					//$check_gid_time = @mysql_query ("Select gid,tstmp_bye from entlehnunglog join deviceentlehnung on entlehnunglog.eid=deviceentlehnung.eid where tstmp_hy IS NULL");
					$check_gid_time = @mysql_query ("Select * from entlehnunglog join deviceentlehnung on entlehnunglog.eid = deviceentlehnung.eid;");
					$write_reserve = "1";
					echo mysql_error();
					while ($ma = mysql_fetch_array($check_gid_time)) {
					$reservationtime = strtotime($date." ".$time);
					if ($ma["GID"] == $device && strtotime($ma["tstmp_bye"]) == $reservationtime) 
					{
						$write_reserve = "0";
					} else {
						$write_reserve = "1";							
					}
				}//end while
				if ($write_reserve == "0") echo "Das Gerät ist zu dieser Zeit leider reserviert!!";
				if ($write_reserve == "1") {
					$reservetime = $date.$time;
					$query = "INSERT INTO entlehnunglog (EID,UID,tstmp_bye) VALUES ('','" . $username . "','" .$reservetime . "');";
					mysql_query ($query);
					echo mysql_error();
					$result = mysql_query ("SELECT EID FROM entlehnunglog order by EID desc LIMIT 0 , 1 ;");
					$row = mysql_fetch_array($result);					
																
																
					$insert = "insert into deviceentlehnung (EID,GID) Values (" . $row["EID"] . "," . $device . ");";
					mysql_query ($insert);							
					echo "Anfrage gesendet\t";
					print "Sie " . $username . " haben sich gerade das Gerät " . $device . " für den " . $date . " um " . $time . " reserviert!";
				}//end if
	
		}
	}
	?>
					</td>
				</tr>
			</table>

<?php
	sas_end();
?>
