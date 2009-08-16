<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Datenbank Erstellen</title>
</head>
<body>
<?php

include ("db.inc");

$query = "Create Database sis";
$result = mysql_query($query);

?>

<script>
window.close();
</script>
</body>
</html>