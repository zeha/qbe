<?
session_start();
include("auth.php");
include("include/db.inc");
include("./include/timecheck.php");
?>   
<HTML>
<HEAD>
<TITLE></TITLE>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
</HEAD>
<BODY onclick=hide_all()>
<?
if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}


switch (isset($_GET['status']) ? $_GET['status'] : '') {
    case "login":
    $db_query = "Select KZ from Lehrer where id=".$userdata['user'];
    $db_query_res = mysql_db_query($db_name,$db_query);    
    /*if (mysql_num_rows($db_query_res)!=0){
        $row = mysql_fetch_array($db_query_res);
        $user = $row['KZ'];
    }*/
        echo '
            <center><h2 style="color: green;">Erfolgreich eingeloggt</h2></center><br>
            <script>
                parent.frames[2].document.location="status.php?status=user&user='.(isset($user) ? $user : '').'&type='.$userdata['rights'].'";
            </script>
            ';
        break;
    case "update":
        echo '
            <center><h2 style="color: green;">Update der Daten erfolgreich</h2></center>';
        break;
    case "delete":
        echo '
            <center><h2 style="color: green;">L&ouml;schen der Daten erfolgreich</h2></center>';
        break;
    case "add":
        echo '
            <center><h2 style="color: green;">Eintragung der Daten erfolgreich</h2></center>';
        break;
    case "ibutton":
    $db_query = "Select KZ from Lehrer where id=".$userdata['user'];
    $db_query_res = mysql_db_query($db_name,$db_query);    
    if (mysql_num_rows($db_query_res)!=0){
        $row = mysql_fetch_array($db_query_res);
        $user = $row['KZ'];
    }
        echo '
            <center><h2 style="color: green;">Erfolgreich per iButton eingeloggt</h2></center><br>
            <script>
                parent.frames[2].document.location="status.php?status=user&user='.$user.'&type='.$userdata['rights'].'&ibutton='.$userdata['ibutton'].'";
            </script>
            ';
        break;

}

switch (isset($_GET['err']) ? $_GET['err'] : '') {
    case "ldap":
        echo '
            
            <center><h2 style="color: red;">LDAP - Fehler</h2></center>
            ';
        break;
    case "data":
        echo '
            
            <center><h2 style="color: red;">Daten nicht eingetragen</h2></center>
            ';
        break;
    case "database":
        echo '
            
            <center><h2 style="color: red;">Database Fehler</h2></center>
            ';
        break;
    case "delete":
        echo '
            
            <center><h2 style="color: red;">Daten nicht gelöscht</h2></center>
            ';
        break;
    case "ibutton":
        echo '
            <center><h2 style="color: red;">Login fehlgeschlagen</h2><h3><u>Reason:</u> iButton not in Database or no iButton connected</h3>
            </center>
            ';
        break;
}

if (auth()==1){
    switch ($userdata['rights']) {
        
        case "l":
            include("include/main_teacher.php");
            break;
        case "s":
            include("include/main_student.php");
            break;
        case "av":
            break;
        case "a":
            break;
    } 
}?>
</BODY>
</HTML>
