<?php
include("include/db.inc");
        $db_query='update termin set check_klasse ="'.date("Y-m-d").'" where id='.$_GET['id'];
        $db_query_res = mysql_db_query($db_name,$db_query);
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