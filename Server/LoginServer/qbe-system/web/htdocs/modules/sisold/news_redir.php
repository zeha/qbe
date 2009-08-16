<?
    session_start();
    include("include/db.inc");
    include("auth.php");
    @include("include/const.php");
    
    
    function add_entry()
    {
        global $userdata, $status_page;
        $db_query='insert into news values(NULL,"'.$userdata['user'].'","'.$_POST['abt'].'","'.date("Y-m-d").'","'.$_POST['news_text'].'","'.$userdata['user'].'","'.date("Y-m-d").'")';
        $db_query_res = mysql_db_query($db_name,$db_query);
        if (mysql_affected_rows()!=1) {
            //echo 'rows: '.$db_query;
            header("Location: $status_page?err=database");
        }else{
            header("Location: $status_page?status=add");
        }
    }
    
    function print_add()
    {
        global $userdata;
        if ($userdata['rights']=='a')
        {
            $pos=array("Alle Abteilunge","Eigene Abteilung","Alle Lehrer","Eigene Lehrer");
        }else{
            $pos=array("Alle Lehrer","Eigene Lehrer");
        }
    include("include/admin_menu.inc");    
    echo '
        <html>
        <head>
        <title>Confirm News</title>
        <link rel=stylesheet href=main.css>
        <script src="main.js">
        </script>
        <script>
        function send(action) {
            document.getElementById(\'form1\').submit();
        }
        </script>
        </head>
        <body onclick=hide_all()>
        <form action="news_redir.php" id="form1" method=post>
        <center>
        <table>
        <tr><td>Position: <select name=position id=position>';
        for ($x=0;$x<sizeof($pos);$x++)
        {
            echo '<option value='.$x.'>'.$pos[$x].'</option>';
        }
        echo '    
        </select></td></tr>
        <tr><td>Nachricht:</td></tr>
        <tr><td><textarea name="news_text" cols="50" rows="10"></textarea></td></tr>
        <tr><td><center><input type=button value="Eintragen" onclick="send();"></td></tr>
        </table>
        <input type=hidden value="add" name="action" id=action>
        <input type=hidden value="1" name="best" id=best>
        <input type=hidden value='.$_POST['abt'].' name="abt" id=abt>
        </bod>
        </html>
        ';
    }

    function print_confirm_del()
    {
    include("include/admin_menu.inc");
    echo '
        <html>
        <head>
        <title>Confirm News</title>
        <link rel=stylesheet href=main.css>
        <script src="main.js">
        </script>
        <script>
        function send(action) {
            document.getElementById(\'best\').value=action;
            document.getElementById(\'form1\').submit();
        }
        </script>
        </head>
        <body onclick=hide_all()>
        <form action="news_redir.php" method=post id="form1">
        <center>
        '.sizeof($_POST['data']);
        if (sizeof($_POST['data'])==1)
        {
            echo ' Eintrag l&ouml;schen<br><br>';
        }else{
            echo ' Eintr&auml;ge l&ouml;schen<br><br>';
        }
    echo '
        <table border=0>
        <tr><td><center><input type=button value="JA" onclick="send(1);" name="ja"></center></td><td><center><input type=button value="NEIN" onclick="send(2);" name="nein"></td></tr>
        </table>
        </center>
        <input type=hidden value="" name="best" id=best>
        <input type=hidden value="del" name="action" id=action>';
    for ($x=0;$x<sizeof($_POST['data']);$x++)
    {
        echo '<input type=hidden value="'.$_POST['data'][$x].'" name=data[]><br>'."\n";
    }
    echo '
        </form>
        </form>
        </body>
        </html>
    ';
    }

if (auth()==0){
    header("Location: ".$login_page);
}

switch ($_POST['action']) 
{
    case "del":
        switch ($_POST['best'])
        {
        case 1:
            del_entry();
            break;
        case 2:
            header("Location: ".$status_page."?err=data");
            break;
        }  
        print_confirm_del();
        break;
    case "add":
        if ($_POST['best']==1)
        {
            add_entry();
            break;
        }
        print_add();
        break;
}
?>