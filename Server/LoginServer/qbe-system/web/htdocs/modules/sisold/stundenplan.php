<?
session_start();
require("auth.php");
require("include/db.inc");
?>   
<HTML>
<HEAD>
<TITLE></TITLE>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
</HEAD>
<BODY onclick=hide_all()>
<?
if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}

if (isset($userdata['Klasse'])) {

?>

<center><h2 style="color: black;">Stundenplan der Klasse: <?php echo (isset($userdata['Klasse']) ? $userdata['Klasse']:''); ?></h2></center>
<br>
<br>
<table align="center" border="1" cellpadding="0" cellspacing="0">
<tr><td></td><td><center><b>Mo</b></center></td><td><center><b>Di</b></center></td><td><center><b>Mi</b></center></td><td><center><b>Do</b></center></td><td><center><b>Fr</b></center></td><td><center><b>Sa</b></center></b></td></tr>
<?php

for ($x = 1; $x < 12; $x++) {?>
<tr><td width="30"><center><?php echo "<b>".$x."</b>"; ?></center></td>
<?php

for ($y = 1; $y < 7; $y++) {

$klassenquery = "Select * from klasse where Name = '".$userdata['Klasse']."'";
$klassenresult = mysql_db_query($db_name, $klassenquery);
$klassenrow = mysql_fetch_array($klassenresult);
$klasse = $klassenrow['id'];

$query = "Select * from stundenplan where WTag = '".$y."' and Stunde = '".$x."' and Klasse = '".$klasse."'";
$result = mysql_db_query($db_name, $query);
$row = mysql_fetch_array($result);

$fachquery = "Select * from fach where id = '".$row['Fach']."'";
$fachresult = mysql_db_query($db_name, $fachquery);
$fachrow = mysql_fetch_array($fachresult);
$fach = $fachrow['Name'];

?>
<td width="70"><center><?php if ($fach == FALSE) {echo "-";} else {echo $fach;}?></center></td>
<?php
}
echo "</tr>";
}
?>
</table>
<?php
} else {
?>
<br>
<br>
<h2><center>Sie sind nicht eingeloggt.</center></h2>
<?php
}
?>

</BODY>
</HTML>