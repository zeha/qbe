<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Stunde</title>
</head>
<body>

<?php
include ("./db.inc");

$row = 1;                                      
$handle = fopen ("stundenplan.TXT","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $Klasse = $data[1];
    $Lehrer = $data[2];
    $Fach = $data[3];
    $WTag = $data[5];
    $Stunde = $data[6];
            
    $kquery = "Select * from klasse where Name = '$Klasse'";
    $kresult = mysql_db_query("sis", $kquery);
    $krow = mysql_fetch_array($kresult);
    
    $lquery = "Select * from lehrer where KZ = '$Lehrer'";
    $lresult = mysql_db_query("sis", $lquery);
    $lrow = mysql_fetch_array($lresult);
    
    $fquery = "Select * from fach where Name = '$Fach'";
    $fresult = mysql_db_query("sis", $fquery);
    $frow = mysql_fetch_array($fresult);
       
    $query = "Insert into stundenplan(WTag, Stunde, Klasse, Fach, Lehrer) values('$WTag', '$Stunde', '".$krow['id']."', '".$frow['id']."', '".$lrow['id']."')";

    $result = mysql_db_query("sis", $query) or die (mysql_error());
   
        }
fclose ($handle);
?>

<script>
window.close();
</script>
</body>
</html>