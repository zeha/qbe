<?php
include("include/db.inc");
switch ($_GET['user']) {
    case "l":
        $db_query='update supplierung set check_lehrer ="'.date("Y-m-d").'" where lfdnr='.$_GET['id'];
        $db_query_res = mysql_db_query($db_name,$db_query);
        break;
    case "s":
        $db_query='update supplierung set check_klasse ="'.date("Y-m-d").'" where lfdnr='.$_GET['id'];
        $db_query_res = mysql_db_query($db_name,$db_query);
        break;
    
}
?>
<html>
<head>
</head>
<body>
<script>
opener.reload();
window.close();
</script>
</body>
</html>