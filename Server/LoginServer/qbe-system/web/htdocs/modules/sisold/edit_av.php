<?
session_start();
include("auth.php");
include("include/db.inc");
$userdata=$_SESSION['userdata'];
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

if (isset($_POST['action']) ? $_POST['action'] : '' == "save") {

$name = array('1' => $_POST['name1'], '2' => $_POST['name2'], '3' => $_POST['name3'], '4' => $_POST['name4'], '5' => $_POST['name5'], '6' => $_POST['name6']);
$kz = array('1' => $_POST['kz1'], '2' => $_POST['kz2'], '3' => $_POST['kz3'], '4' => $_POST['kz4'], '5' => $_POST['kz5'], '6' => $_POST['kz6']);
$id = array('1' => $_POST['id1'], '2' => $_POST['id2'], '3' => $_POST['id3'], '4' => $_POST['id4'], '5' => $_POST['id5'], '6' => $_POST['id6']);

for ($x = 1; $x < 7; $x++) {

$updatequery = "UPDATE `av` SET `name` = '".$name[$x]."', `kz` = '".$kz[$x]."' where id = '".$id[$x]."'";

$updateresult = mysql_db_query($db_name, $updatequery) or die(mysql_error());

}

if ($updateresult == TRUE) {echo "<center><h2 style='color: green;'>Eintrag erfolgreich!</h2></center>";} else {echo "<center><h2 style='color: green;'>Eintrag fehlgeschlagen!</h2></center>";}

} else {

$avquery = "Select * from av where abteilung = '".$_GET['Abteilung']."' and tag = 'AV'";
$avresult = mysql_db_query($db_name, $avquery) or die (mysql_error());
$avrow = mysql_fetch_array($avresult) or die (mysql_error());

?>
<table border="0"><tr><td><b>Abteilungsvorstand: </b></td><td><?php echo $avrow['name']; ?></td></tr></table>
<br>
<br>
<form action='edit_av.php' method='post'>
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
<td width='150'><input type='text' name='name<?php echo $x; ?>' value='<?php echo $svrow['name']; ?>'></td><td><input type='text' name='kz<?php echo $x; ?>' value='<?php echo $svrow['kz'] ?>'></td><td><input type='hidden' name='id<?php echo $x;?>' value='<?php echo $svrow['id']; ?>'></td></tr><?php } ?></table><br><br>
<?php

if ($userdata['rights'] == "a") {
?>
<input type='hidden' value='save' name='action'>
<input type='submit' value='Speichern' name='save'>
</form>
<?php } }?>


</BODY>
</HTML>