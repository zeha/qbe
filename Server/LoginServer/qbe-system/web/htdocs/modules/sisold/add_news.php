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
<?php
if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}

if (isset($_POST['eintragen']) ? $_POST['eintragen'] : '' == 'Eintragen') {

$insertquery = "Insert into news values ('', '".$_POST['datum']."', '".$_POST['text']."', '".$_POST['lehrer']."', '".$_POST['abteilung']."')";
$insertresult = mysql_db_query($db_name, $insertquery);

if ($insertresult == TRUE) {echo "<center><h2 style='color: green;'>Eintrag erfolgreich!</h2></center>";} else {echo "<center><h2 style='color: green;'>Eintrag fehlgeschlagen!</h2></center>";}

} else {

?>
<br><br><br>
<form action='add_news.php' method='post'>
<input type='text' name='datum' value='<?php echo date("Y-m-d H:i:s");?>'><br><br>
<textarea name='text' style='width: 400; height:200;'></textarea><br><br>
<select name='lehrer'>
<?
            $db_query='Select * from lehrer order by KZ';
            $db_query_res = @mysql_db_query($db_name,$db_query);
            if (@mysql_num_rows($db_query_res)!=0){
                while($row = @mysql_fetch_array($db_query_res)) {
                    echo '<option value='.$row['id'].'>'.$row['KZ'].'</option>';
                }
            }
        ?>
</select><br><br>
<select name='abteilung'>
<?php
			$db_query2='Select * from abteilung order by Name';
			$db_query_res2=mysql_db_query($db_name,$db_query2);
			if (mysql_num_rows($db_query_res2) != 0) {
				while($row = mysql_fetch_array($db_query_res2)) {
					echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
				}
			}
		?>
</select> <br><br>
<input type='submit' name='eintragen' value='Eintragen'>
</form>
<?php } ?>
</BODY>
</HTML>