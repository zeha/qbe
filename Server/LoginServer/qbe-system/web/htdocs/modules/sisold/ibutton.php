<?php
include("include/ibutton.php");
$host = "127.0.0.1";
$port = 13500;
echo "IButton Connected to Server: ".get_ibutton($host,$port);
?>

<html>
<head>
</head>
<body>
    <script>
        //parent.frames[2].document.location = "status.php?mode=ibutton&host=<?=$host?>&port=<?=$port?>&refresh="
    </script>
</body>
</hmtl>