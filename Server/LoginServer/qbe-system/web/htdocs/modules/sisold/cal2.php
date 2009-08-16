<?php
session_start();
require("include/db.inc");
require("auth.php");
require("include/sup-check.inc");
error_reporting(15);

function week_norm() {
    global $zeit,$db_name,$color;
    echo '<TABLE cellSpacing=0 cellPadding=0 border=0 class="kal">';
    echo '<tr><th colspan=2 class="kal" style="width: 95px; background-image: url(Pictures/kall.png);">Datum</th><th class="kal" style="width: 70px;">Kategorie</th><th class="kal" style="width: 110px;">Zeit</th><th class="kal" style="width: 400px;">Beschreibung</th><th class="kal">&nbsp;</th><th class="kal" style="width: 90px; background-image: url(Pictures/kalr.png);">&nbsp;</th></tr>';
    for ($x=1;$x<8;$x++){ //Woche generieren
        if ($x%2==0){
            echo '<tr class="kal" style="BACKGROUND-COLOR: #ffffff">';
        }else{
            echo '<tr class="kal" style="BACKGROUND-COLOR: '.$color['cell'].'">';
        }
        echo '<td>';
        echo date("D",$zeit);
        echo '</td><td>';
        echo  date("d.m.y",$zeit);
        echo '</td><td></td><td></td><td></td><td></td><td></td></tr>';
        $db_query='
        select
            termin.time as time,
            termin.text as text,
            abteilung.kz as abteilung,
            kat.kurzbez as type,
            termin.raumnr as raum,
            lehrer.KZ as lehrer,
            termin.CN as rechner,
            termin.typ as type_id
        from
            termin
			inner join lehrer on (termin.lehrer = lehrer.id)
            inner join abteilung on (abteilung.id = termin.abteilung)
            inner join kat on (kat.id = termin.typ)
        where
            termin.date = "'.date("Y-m-d",$zeit).'"
            and
            kat.private = 0
            and
            (termin.abteilung = '.$_GET['Abteilung'].'
            or
            termin.abteilung = 0)';
       // echo $db_query;
        $db_query_res = mysql_db_query($db_name,$db_query) or die(mysql_error());
        if (mysql_num_rows($db_query_res)!=0){
        while ($row = @mysql_fetch_array($db_query_res)){
            if ($x%2==0){
                echo '<tr class="kal" style="BACKGROUND-COLOR: #ffffff">';
            }else{
                echo '<tr class="kal" style="BACKGROUND-COLOR: '.$color['cell'].'">';
            }
            echo '<td colspan=2></td>';
            echo '<td><center>'.$row['type'].'</center></td><td><center>'.$row['time'].'</center></td><td><center>';
            switch ($row['type_id']) {
                case 5:
                    echo $row['lehrer'].' : '.$row['text'];
                    break;
                case 2:
                    echo $row['raum'].' : '.$row['text'].' / '.$row['lehrer'];
                    break;
                case 3:
                    echo $row['rechner'].' : '.$row['text'];
                    break;
                default:
                    echo $row['text'];
                    break;
            }
            echo '</center></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        }
        }
        //(echo '<tr><td></td><td>'.$db_query.'</td></tr>';
        $zeit = $zeit+86400;
    }
    echo '</table>';
}
?>
<HTML>
<HEAD>
<TITLE></TITLE>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
<script>
function send_term(){
document.getElementById('form1').action="add_term.php";
document.getElementById('form1').submit();
}
function send_sup() {
document.getElementById('form1').action="add_sup.php";
document.getElementById('form1').submit();
}
</script>
</HEAD>
<BODY onclick=hide_all()>
<form id=form1>
<? 
    if (auth()==1){
        include("include/admin_menu.inc");
    }else{
        include("include/menu.inc");
    }
    
    switch ($_GET['Abteilung']){
        case "1":
            $color = array('head'=>'#0090ff','cell'=>'#CCEEFF');
            $abteilung = array('name'=>'E-Technik','id'=>1);
            break;
        case "0":
            $color = array('head'=>'#0090ff','cell'=>'#E5E5E5');
            $abteilung = array('name'=>'Allgemein','id'=>0);
            break;            
        case "2":
             $color = array('head'=>'#0090ff','cell'=>'#FFCCCC');
            $abteilung = array('name'=>'AUT-Technik','id'=>2);
            break;           
        case "4":
            $color = array('head'=>'#0090ff','cell'=>'#FFFFB3');
            $abteilung = array('name'=>'Hochbau','id'=>4);
            break;
    }
    
echo '<h1><center>Kalender der Abteilung: '.$abteilung['name'].'</center></h1>';

    $zeit = time();
    $datum = getdate($zeit);
    $zeit=$zeit - 86400*($datum['wday']-1);
    $von = date("d.m.y",$zeit);
    $bis = date("d.m.y",$zeit+86400*6);
    //echo $von." - ".$bis;
    
    week_norm();
    echo '<br><br>';
    week_norm();
    echo '<br><br>';
    week_norm();
    echo '<br><br>';
    week_norm();
?>
<br>
<center>
<input type=hidden name=abt value=<?=$abteilung['id']?>>
<? if ((isset($userdata['rights']) ? $userdata['rights'] : '') == "a" or ((isset($userdata['rights']) ? $userdata['rights'] : '') == "av" and ((isset($userdata['abteilung']) ? $userdata['abteilung'] :'')==$abteilung['id'] or $userdata['abteilung']==0))) {
    echo '
    <table border=0>
    <tr><td><input type=button onclick="send_term()" value="Neuer Termin"></td><td><input type=button onclick="send_sup()" value="Neue Supplierung"></td></tr>
    </table>';
} elseif ((isset($userdata['rights']) ? $userdata['rights'] : '')=="l" and ($userdata['abteilung']==0 or $userdata['abteilung']==$abteilung['id'])) {
    echo '
    <table border=0>
    <tr><td><input type=button onclick="send_term()" value="Neuer Termin"></td></tr>
    </table>';
}
?>
</center>
</form>
</body>
</html>
