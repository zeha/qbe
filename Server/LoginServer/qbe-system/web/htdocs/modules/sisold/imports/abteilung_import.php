<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Abteilung</title>
</head>
<body>

<?php
include ("db.inc");

$row = 1;
$handle = fopen ("abteilung.csv","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);

    $id = $data[0];
    $Name = $data[1];
    $KZ = $data[2];

    $query = "Insert into abteilung(id, Name, KZ) values($id, '$Name', '$KZ')";

    $result = mysql_db_query("sis", $query) or die (mysql_error());
    
        }
fclose ($handle);
?>

<script>
window.close();
</script>
</body>
</html>