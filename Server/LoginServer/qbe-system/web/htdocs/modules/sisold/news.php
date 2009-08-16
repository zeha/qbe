<?
session_start();
include("include/db.inc");
include("auth.php");
include("include/const.php");
$userdata = isset($_SESSION['userdata']) ? $_SESSION['userdata'] : array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());?>
<html>
<head>
        <title>News</title>
<link rel=stylesheet href=main.css>
<script src="main.js">
</script>
<script src="news.js">
</script>
</head>
<body bgcolor=#b2b2b2 onclick=hide_all()>
<form action="news_redir.php" method=post id="form1">
<?    
if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}

if ($_GET['Type']=="Lehrer"){
    switch ($_GET['Abteilung']){
        case "1":
            $color='#66ccff';
            $Abteilung='E-Technik';
            break;
        case "2":
            $color='#ff8282';
            $Abteilung='AUT-Technik';
            break;
        case "0":
            $color='#dedede';
            $Abteilung='Allgemein';
            break;
        case "4":
            $color='#ffff66';
            $Abteilung='Hochbau';
            break;
    }
    ?><H1><center>Lehrer-Infos der Abteilung: <?echo $Abteilung;?></center></h1><?    
    $db_query='
    select
        news.Datum as datum,
        lehrer.name as Name,
        news.Text as text,
        lehrer.kz as kz
    from 
        news
        inner join lehrer on (news.lehrer=lehrer.id)  
    where 
        news.Abteilung="'.$_GET['Abteilung'].'"';
}
if ($_GET['Type']=="Abteilung"){
    
    switch ($_GET['Abteilung']){
        case "1":
            $color='#66ccff';
            $Abteilung='E-Technik';
            break;
        case "2":
            $color='#ff8282';
            $Abteilung='AUT-Technik';
            break;
        case "0":
            $color='#dedede';
            $Abteilung='Allgemein';
            break;
        case "4":
            $color='#ffff66';
            $Abteilung='Hochbau';
            break;
    }
    if ($_GET['Abteilung']=="Al"){
    ?><H1><center>Allgemeine Infos </center></h1><?
    }else{
    ?><H1><center>Infos der Abteilung: <?echo $Abteilung;?></center></h1><?
    }
    $db_query='
    select
        news.Datum as datum,
        lehrer.Name as Name,
        news.Text as text,
        lehrer.kz as kz
    from 
        news
        inner join lehrer on (news.lehrer=lehrer.id)  
    where 
        news.Abteilung="'.$_GET['Abteilung'].'"';
}
//echo $db_query."\n";
$db_query_res = @mysql_db_query($db_name,$db_query);
if (@mysql_num_rows($db_query_res)!=0){  
    ?><TABLE cellSpacing=0 cellPadding=0 width="770" border=0 id=TABLE1 class="news"><?
    while($row = mysql_fetch_array($db_query_res)) {
        echo '<tr><td style="BACKGROUND-COLOR: '.$color.'"><a target="_new" href="http://'.$server.'/~'.$row["kz"].'">'.$row['Name'] . "</a> (" . $row['datum']."):".'</td>';
        echo '</tr>';
        echo '<tr><td style="BACKGROUND-COLOR: #ffffff">'.$row['text'].'</tr></td>';   
    }
    ?></table><br><?
}else{
    echo '<H2>Keine Infos vorhanden</H2>';
}
?>
<input type=hidden id=action name=action value="">
<input type=hidden id=abt name=abt value=<?=$_GET['Abteilung']?>>
</form>

<?php
if ((isset($userdata['rights']) ? $userdata['rights'] : '') == "a" or ((isset($userdata['rights']) ? $userdata['rights'] : '') == "av" and (isset($userdata['abteil']) ? $userdata['abteil'] : '') == $_GET['Abteilung'])) {
?><form action='add_news.php' method='post'>
<input type='hidden' name='action' value='add'>
<input type='submit' value='Eintragen'>
</form>
<?php }
?>
</body>
</html>
