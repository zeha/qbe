<?
session_start(); 
include("./include/ibutton.php");
$userdata = isset($_SESSION['userdata']) ? $_SESSION['userdata'] : array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());
?>
<html>
<head>
    <title>Status</title>
    <link rel=stylesheet href=main.css>
    <script src=main.js>
    </script>
    <? if (isset($_GET['ibutton'])) {
        if (get_ibutton("127.0.0.1",13500) != $_GET['ibutton']) {
            echo '<script>
                    parent.frames[1].document.location="login.php?logout=1";
                  </script>';
        }else{
        echo '<script>
                function ibutton_reload() {
                document.location = "status.php?status=user&type='.$_GET['type'].'&ibutton='.$_GET['type'].'&user='.$_GET['user'].'";
                }
                window.setTimeout("ibutton_reload()",10000);
              </script>
                ';
        }}
    ?>
</head>
<body>
<center>
<table class="status">
<?php
$standardscript ='onMouseOver="cursor_hand(this);" '; //'onMouseOver=\'cursor_hand(me);\' ';
switch (isset($_GET['status']) ? $_GET['status'] : '') {
    case "user":
        $status = 'USER: '.$userdata['user'];
        $statusscript = $standardscript.'onClick = \'jump_daten("login.php");\'';
        $style = 'style="color: blue;"';
        switch ($_GET['type']) {
        case "a":
            $type = 'Type: Admin';
            break;
        case "av":
            $type = 'Type: AV';
            break;
        case "l":
            $type = 'Type: Lehrer';
            break;
        case "s":
            $type = 'Type: Sch&uuml;ler';
            break;
        }
        break;
    default:
        $status = 'Login';
        $style = 'style="color: red; font-weight=bolder;"';
        $statusscript = $standardscript.'onClick = \'jump_daten("login.php");\'';
}
echo '<tr><td width=100 '.$style.' '.$statusscript.'>'.$status.'</td><td></td><td width=100>'.(isset($type) ? $type : '').'</td></tr>';
?>
</table>
</center>
</body>
</html>