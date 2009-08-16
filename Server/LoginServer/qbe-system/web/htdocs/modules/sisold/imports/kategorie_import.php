<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Import Kategorie</title>
</head>
<body>

<?php
include ("db.inc");

$row = 1;                                      
$handle = fopen ("kategorie.csv","r");
while ($data = fgetcsv ($handle, 1000, ";")) {

    $num = count ($data);
    
    $id = $data[0];
    $Bezeichnung = $data[1];
    $KurzBez = $data[2];
    $showit = $data[3];
    $private = $data[4];
    
    $query = "Insert into kat(id, Bezeichnung, KurzBez, showit, private) values($id, '$Bezeichnung', '$KurzBez', $showit, $private)";

    $result = mysql_db_query("sis", $query) or die (mysql_error());
    
        }
fclose ($handle);
?>

<script>
window.close();
</script>
</body>
</html>