<?
session_start();
include("auth.php");
include("include/db.inc");
$userdata=isset($_SESSION['userdata']) ? $_SESSION['userdata'] : '';
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

$avquery = "Select * from av where abteilung = '".$_GET['Abteilung']."' and tag = 'AV'";
$avresult = mysql_db_query($db_name, $avquery) or die (mysql_error());
$avrow = mysql_fetch_array($avresult) or die (mysql_error());

?>
<table border="0"><tr><td><b>Abteilungsvorstand: </b></td><td><?php echo $avrow['name']; ?></td></tr></table>
<br>
<br>
<b>Stellvertreter: </b>
<table border="0">
<?php
$tag = array('1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday');

for ($x = 1; $x <= 6; $x++) {

$svquery = "Select * from av where abteilung = '".$_GET['Abteilung']."' and tag = '".$tag[$x]."'";
$svresult = mysql_db_query($db_name, $svquery) or die(mysql_error());
$svrow = mysql_fetch_array($svresult);

echo "<tr><td width='100'>";

switch($x) {
	case 1: $wtag = "Montag";
	break;
	case 2: $wtag = "Dienstag";
	break;
	case 3: $wtag = "Mittwoch";
	break;
	case 4: $wtag = "Donnerstag";
	break;
	case 5: $wtag = "Freitag";
	break;
	case 6: $wtag = "Samstag";
	break;
	}
echo "".$wtag.": </td>";
?>
<td width='150'><?php echo $svrow['name']; }?></td></tr></table>
<?php

if ((isset($userdata['rights']) ? $userdata['rights'] :'') == "a") {
?><br><br>
<form action='edit_av.php' method='GET'>
<input type='hidden' name='edit' value='1'>
<input type='hidden' name='Abteilung' value='<?php echo $_GET['Abteilung'];?>'>
<input type='submit' value='Ändern'>
</form>
<?php } ?>


</BODY>
</HTML>