<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Stunde</title>
</head>
<body>

<?php
include ("db.inc");

$row = 1;                                      
$handle = fopen ("stunde.csv","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $id = $data[0];
    $start = $data[1];
    $ende = $data[2];

    $query = "Insert into stunde(id, start, ende) values($id, '$start', '$ende')";

    $result = mysql_db_query("sis", $query) or die (mysql_error());
    
        }
fclose ($handle);
?>
<script>
window.close();
</script>
</body>
</html>