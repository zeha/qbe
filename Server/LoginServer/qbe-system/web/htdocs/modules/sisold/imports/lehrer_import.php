<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Lehrer</title>
</head>
<body>

<?php
include ("./db.inc");
$id = 1;
$row = 1;                                      
$handle = fopen ("lehrer.TXT","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $KZ = $data[0];
    $nachName = $data[1];
    $vorName = $data[28];
    $name = $nachName." ".$vorName;
    
    $query = "Insert into lehrer(id, KZ, Name) values('$id', '$KZ', '$name')";

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