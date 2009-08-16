<?
session_start();
include("auth.php");
include("include/db.inc");
$userdata= isset($_SESSION['userdata']) ? $_SESSION['userdata'] : '' ;
?>   
<HTML>
<HEAD>
<TITLE></TITLE>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
</HEAD>
<BODY onclick=hide_all()>
<?php
if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}

if (isset($_POST['search']) and $_POST['search'] == 'search') {

$query = "Select * from stundenplan where Lehrer = '".$_POST['lehrer']."' and WTag = '".$_POST['tag']."' and Stunde = '".$_POST['stunde']."'";
$result = mysql_db_query($db_name, $query);
$row = mysql_fetch_array($result);

if ($row['Klasse'] == NULL) {$row['Lehrer'] = NULL;} else {

$lehrerquery = "Select * from lehrer where id = '".$row['Lehrer']."'";
$lehrerresult = mysql_db_query($db_name, $lehrerquery);
$lehrerrow = mysql_fetch_array($lehrerresult);

$klassenquery = "Select * from klasse where id = '".$row['Klasse']."'";
$klassenresult = mysql_db_query($db_name, $klassenquery);
$klassenrow = mysql_fetch_array($klassenresult);

$fachquery = "Select * from fach where id = '".$row['Fach']."'";
$fachresult = mysql_db_query($db_name, $fachquery);
$fachrow = mysql_fetch_array($fachresult);
}
?>
<center><h2 style="color: black;">Suchergebnis: </h2></center>
<br>
<?php if ($row['Lehrer'] == NULL) {echo "<center>Der Lehrer hat in dieser Stunde keine Klasse</center>";} else {
?>
<table align="center" border="1" cellpadding="1" cellspacing="0">
<tr><td><center><b>Lehrer: </b></center></td><td><center><b>Klasse: </b></center></td><td><center><b>Fach: </b></center></td></tr>
<tr><td width="170"><center><?php echo ucwords(strtolower($lehrerrow['Name'])); ?></center></td><td width="100"><center><?php echo $klassenrow['Name']; ?></center></td><td width="100"><center><?php echo $fachrow['Name']; ?></center></td></tr>
</table>
<?php 
} } else {
?>
<center><h2 style="color: black;">Lehrersuche:</h2></center>

<form action="teachersearch.php" method="post">
Lehrer: 
<select name=lehrer>
        <?
            $db_query='select * from lehrer order by kz';
            $db_query_res = @mysql_db_query($db_name,$db_query);
            if (@mysql_num_rows($db_query_res)!=0){
                while($row = @mysql_fetch_array($db_query_res)) {
					echo '<option value='.$row['id'].'>'.ucwords(strtolower($row['Name'])).'</option>';
                }
            }
        ?>
</select>
Tag:
<?php
$tag = array('1' => 'Montag', '2' => 'Dienstag', '3' => 'Mittwoch', '4' => 'Donnerstag', '5' => 'Freitag', '6' => 'Samstag');
?>
<select name=tag>
		<?php
			
			for ($y = 1; $y < 7; $y++) {
				echo '<option value='.$y.'>'.$tag[$y].'</option>';
			}
		?>
</select>

Stunde: 
<select name=stunde>
		<?
		for ($x = 1; $x < 12; $x++) {
		echo '<option value='.$x.'>'.$x.'</option>';
		}
		?>
</select>
<input type="hidden" name="search" value="search">
<input type="submit" name="Login" value="Search">
</form>
<?php
}
?>
</BODY>
</HTML>