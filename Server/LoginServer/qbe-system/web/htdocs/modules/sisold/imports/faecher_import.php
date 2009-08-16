<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Stunde</title>
</head>
<body>

<?php
include ("./db.inc");
$id = 1;
$row = 1;                                      
$handle = fopen ("faecher.TXT","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $Name = $data[0];
    $Beschreibung = $data[1];

    $query = "Insert into fach(id, Name, Beschreibung) values('$id', '$Name', '$Beschreibung')";

    $result = mysql_db_query("sis", $query) or die (mysql_error());


    $id++;    
        }
fclose ($handle);
?>

<script>
window.close();
</script>
</body>
</html>