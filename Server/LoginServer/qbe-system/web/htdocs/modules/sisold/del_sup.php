<?php
include("include/db.inc");
switch ($_GET['id']) {
    case '*':
        $db_query='delete from supplierung where 1>0';
        $db_query_res = @mysql_db_query($db_name,$db_query);
        header("Location: test1.php?status=delete");
        break;
    default:
        $db_query='select datum, statt_lehrer as lehrer from supplierung where lfdnr='.$_GET['id'];
        $db_query_res = mysql_db_query($db_name,$db_query);
        $row = mysql_fetch_array($db_query_res);
        $db_query='delete from termin where date="'.$row['datum'].'" and lehrer='.$row['lehrer'];
        echo $db_query;
        $db_query_res = mysql_db_query($db_name,$db_query);
        $db_query='delete from supplierung where lfdnr='.$_GET['id'];
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