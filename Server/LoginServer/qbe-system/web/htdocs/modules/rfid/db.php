<?php
require("../../sas.inc.php");
sas_start('Devices in Database',"../../","/modules/rfid/",1);
require 'head.php';
?>						
			<!--table width="100%" cellpadding="0" cellspacing="0" class="borderoff"-->
										
				<?php
					
					$lsql = $link;
					mysql_select_db("inventardb");
					if (isset($_POST["gid"]) && !empty($_POST["gid"]) &&
						isset($_POST["bez"]) && !empty($_POST["bez"]) &&
						isset($_POST["inr"]) && !empty($_POST["inr"]) &&
						isset($_POST["rnr"]) && !empty($_POST["rnr"]) &&
						isset($_POST["knr"]) && !empty($_POST["knr"]) &&
						isset($_POST["pnr"]) && !empty($_POST["pnr"])) {
							$sql_string = "INSERT INTO devices VALUES('" . $_POST["gid"] . "','" . $_POST["inr"] . "','" . $_POST["rnr"] . "','" . $_POST["knr"] . "','" . $_POST["pnr"] . "','" . $_POST["bez"] . "')";
							mysql_query ($sql_string);
					}
											
					if (isset($_POST["gid"]) && isset($_POST["erase"])) {
												
						$query = "delete from devices where gid= " . $_POST["gid"] . ";";
						mysql_query ($query); 
												
												
					}
				qbe_web_maketable(true);									
				?>
										
			<tr width="100%">
				<th>
				GID
				</th>
				<th>
				Bezeichnung
				</th>
				<th>
				Inventarnummer
				</th>
				<th>
				Raumnummer
				</th>
				<th>
				KastenNr.
				</th>
				<th>
				PlatzNr.
				</th>
			</tr>
			<?php
				$res = @mysql_query ("select * from devices order by gid asc");
				while ($row = mysql_fetch_array($res)) {
					qbe_web_maketr();
					printf ("<td width=\"120\">%s</td><td width=\"120\">%s</td><td width=\"120\">%s</td><td width=\"120\">%s</td><td width=\"120\">%s</td><td width=\"120\">%s</td>",
							$row["GID"],
							$row["text"],
							$row["invnr"],
							$row["rnr"],
							$row["knr"],
							$row["pnr"]);
				}
		?>	
			<tr>
			<form action="db.php" method="post">
			<td><input type="text" name="gid"></td>
			<td><input type="text" name="bez"></td>
			<td><input type="text" name="inr"></td>
			<td><input type="text" name="rnr"></td>
			<td><input type="text" name="knr"></td>
			<td><input type="text" name="pnr"></td>
			</tr>
		<tr align="center">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><button type="submit" size="10">submit</button></td></tr>
		</form>
		</table>
			<h2>Alter Database Entry</h2>
		<?php
		qbe_web_maketable(true);
		?>
				<tr>	
				<th>
				GID
				</th>
				<th>
				Bezeichnung
				</th>
				<th>
				Inventarnummer
				</th>
				<th>
				Raumnummer
				</th>
				<th>
				KastenNr.
				</th>
				<th>
				PlatzNr.
				</th>
				</tr>
	<?php
	qbe_web_maketr();

	print ("<form action=\"db.php\" method=\"post\">
		<td><input type=\"text\" name=\"alter_gid\"></td>
		<td><input type=\"text\" name=\"alter_bez\"></td>
		<td><input type=\"text\" name=\"alter_inr\"></td>
		<td><input type=\"text\" name=\"alter_rnr\"></td>
		<td><input type=\"text\" name=\"alter_knr\"></td>
		<td><input type=\"text\" name=\"alter_pnr\"></td>");
	?>
	<tr>
		<td colspan="5">Bitte geben Sie auch bei Änderung von nur einem Attribut das volle Attribut Set an!</td>
		<td align="center"><button type="submit" size="10">alter</button></td>
	</tr></form>
	</table>
	<?php
		
	$alter_gid = isset($_REQUEST['alter_gid']) ? $_REQUEST['alter_gid'] : '';
	$alter_bez = isset($_REQUEST['alter_bez']) ? $_REQUEST['alter_bez'] : '';
	$alter_inr = isset($_REQUEST['alter_inr']) ? $_REQUEST['alter_inr'] : '';
	$alter_rnr = isset($_REQUEST['alter_rnr']) ? $_REQUEST['alter_rnr'] : '';
	$alter_knr = isset($_REQUEST['alter_knr']) ? $_REQUEST['alter_knr'] : '';
	$alter_pnr = isset($_REQUEST['alter_pnr']) ? $_REQUEST['alter_pnr'] : '';
		
		if ($alter_gid && $alter_bez && $alter_inr && $alter_rnr && $alter_knr && $alter_pnr) {

			$new_entry = "Update devices set text = '" .$alter_bez. "',invnr = '" .$alter_inr. "',rnr = '" .$alter_rnr. "',knr = '" .$alter_knr. "',pnr = '" .$alter_pnr. "' where gid = '" .$alter_gid. "';";
			mysql_query ($new_entry);
	}

	print ("<h2>Delete Entry from Database</h2>");
	qbe_web_maketable(true);
	?>	
	<th>
	Device ID
	</th>
	<tr>
		<td>		
		<form action="db.php" method="post">
		<input type="text" name="gid" size="15">&nbsp&nbsp
		<button type="submit" size="10">delete</button>
			</td>
	</tr>
</table>

<?php
	sas_end();
?>
