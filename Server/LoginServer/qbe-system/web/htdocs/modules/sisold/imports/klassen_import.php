<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Stunde</title>
</head>
<body>

<?php
include ("./db.inc");
$i = 1;
$row = 1;                                      
$handle = fopen ("klassen.TXT","r");

while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $Name = $data[0];
    $Abteilung = $data[1];
    
    if ($Abteilung == "INFORMATIONSTECHNIK-E" || $Abteilung == "AS-ELEKTROTECHNIK" || $Abteilung == "AS-INFORMATIONSTECHNIK-E" || $Abteilung == "ELEKTROTECHNIK-FACHSCHULE") {$Abteilung = "ELEKTROTECHNIK";} 

    if ($Abteilung == "EDVO-KOLLEG") {$Abteilung = "EDVO";} 
    
    if ($Abteilung == "AS-HOCHBAU") {$Abteilung = "HOCHBAU";} 
    
    if ($Abteilung == "AS-AUTOMATISIERUNGSTECHNIK") {$Abteilung ="AUTOMATISIERUNGSTECHNIK";} 
        
    $iquery = "Select * from abteilung where Name = '$Abteilung'";
    $iresult = mysql_db_query("sis", $iquery);
    $irow = mysql_fetch_array($iresult);
    
    $id = $irow['id'];
    
    $query = "Insert into klasse(id, Abteilung, Name) values('$i', '$id', '$Name')";

    $result = mysql_db_query("sis", $query) or die (mysql_error());   
      
    $i++;  
      }
fclose ($handle);
?>

<script>
window.close();
</script>
</body>
</html>