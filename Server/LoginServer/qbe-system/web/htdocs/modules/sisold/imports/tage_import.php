<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Stunde</title>
</head>
<body>

<?php
include ("db.inc");

$row = 1;                                      
$handle = fopen ("tage.csv","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $id = $data[0];
    $Name = $data[1];

    $query = "Insert into tage(id, Name) values($id, '$Name')";

    $result = mysql_db_query("sis", $query) or die (mysql_error());

        }
fclose ($handle);
?>

<script>
window.close();
</script>
</body>
</html>