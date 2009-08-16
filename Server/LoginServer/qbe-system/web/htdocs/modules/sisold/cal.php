<?
session_start();
$db_server="titan";
$db_user="root";
$db_passwd="";
$db_name="sis";

$db = MYSQL_CONNECT($db_server,$db_user,$db_passwort) or die ("Konnte keine Verbindung zur Datenbank herstellen");

include("auth.php");
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
            $color = array(head=>'#0090ff',cell=>'#80D4FF');
            $abteilung = array(name=>'E-Technik',id=>1);
            break;
        case "0":
            $color = array(head=>'#0090ff',cell=>'#d9d9d9');
            $abteilung = array(name=>'Allgemein',id=>0);
            break;            
        case "2":
             $color = array(head=>'#0090ff',cell=>'#FF8080');
            $abteilung = array(name=>'AUT-Technik',id=>2);
            break;           
        case "4":
            $color = array(head=>'#0090ff',cell=>'#FFFF77');
            $abteilung = array(name=>'Hochbau',id=>4);
            break;
    }
if ($abteilung[id]==0){
    echo '<h1><center>Allgemeiner Kalender</center></h1>';
}else{
    echo '<h1><center>Kalender der Abteilung: '.$abteilung[name].'</center></h1>';
}
?>
<TABLE cellSpacing=0 cellPadding=0 border=1 class="kal">
<?
if ($abteilung[id]!=0){
    $zeit = time(); // Aktuelle Zeit in Sekunden
    $datum = getdate($zeit);
    
    $zeit=$zeit - 86400*($datum[wday]-1);
    for ($x=1;$x<7;$x++){ //Woche generieren
        if ($x%2==0){
            echo '<tr style="BACKGROUND-COLOR: #ffffff">';
        }else{
            echo '<tr style="BACKGROUND-COLOR: '.$color[cell].'">';
        }
        $datum = getdate($zeit);
        $date="$datum[year]-$datum[mon]-$datum[mday]";
        echo '<td width=95>';
        echo date("D d.m.y",$zeit);
        echo '</td><td>';
        $db_query3='
                select 
                    kalender.Datum as datum, 
                    kalender.Zeit as zeit,
                    kalender.Beschreibung as beschreibung,
                    kalender.Abteilung as abteilung,
                    kalender.hd as hd
                from
                    kalender
                where 
                    datum="'.$date.'"
                    and abteilung='.$abteilung[id].'
                order by kalender.datum asc';
        $db_query_res3 = @mysql_db_query($db_name,$db_query3);
        echo '<TABLE style="WIDTH: 100%" cellSpacing=0 cellPadding=0 width="100%" border=0>';
        while ($row3 = @mysql_fetch_array($db_query_res3)){
            if ($row3[hd]==0){
                echo '<tr><td width=33>'.$row3[zeit].'</td><td>'.$row3[beschreibung].'</td></tr>';
            }else{
                echo '<tr><td width=33>&nbsp;</td><td style="color: #CCCC00">'.$row3[beschreibung].'</td></tr>';
            }
        }
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        echo '</table>';
        $db_query1='select distinct 
                        supplierung.klasse as klasse, 
                        klasse.name as name 
                        from 
                        supplierung 
                        inner join klasse on (supplierung.klasse=klasse.id) 
                        where supplierung.Datum="'."$datum[year]-$datum[mon]-$datum[mday]".'"
                        and supplierung.Abteilung='.$abteilung[id];
        $db_query_res1 = @mysql_db_query($db_name,$db_query1);
        #echo @mysql_num_rows($db_query_res1)."<br>";
        $num_row1=@mysql_num_rows($db_query_res1);
        if (@mysql_num_rows($db_query_res1)!=0){
            while($row1 = mysql_fetch_array($db_query_res1)) {
                echo '<TABLE style="WIDTH: 100%" cellSpacing=0 cellPadding=0 width="100%" border=0 id=tabl_'.$row1[name].'>';
                $db_query2='
                select 
                    klasse.name as Klasse, 
                    supplierung.Stunde as Stunde, 
                    lehrer1.kz as Sup_Lehrer, 
                    fach1.name as Sup_Fach, 
                    lehrer2.kz as Statt_Lehrer, 
                    fach2.name as Statt_Fach,
                    supplierung.Entfaellt as ent,
                    supplierung.Mitauf as mitauf
                from 
                    supplierung 
                    inner join klasse on (supplierung.klasse = klasse.id) 
                    inner join lehrer as lehrer1 on (supplierung.sup_lehrer = lehrer1.id) 
                    inner join lehrer as lehrer2 on (supplierung.statt_lehrer = lehrer2.id) 
                    inner join fach as fach1 on (supplierung.sup_fach = fach1.id)
                    inner join fach as fach2 on (supplierung.statt_fach = fach2.id)                    
                where 
                    supplierung.Datum="'."$datum[year]-$datum[mon]-$datum[mday]".'" 
                    and supplierung.klasse='.$row1[klasse].'
                    and supplierung.abteilung='.$abteilung[id].' 
                order by supplierung.Stunde asc';
                $db_query_res2 = @mysql_db_query($db_name,$db_query2);
                echo '<tr><td width=33 rowspan='.@mysql_num_rows($db_query_res2).'><u>'.$row1[name].':</u></td>';
                #echo "<br>".$db_query2;            
                #echo "<br> ".@mysql_num_rows($db_query_res2);
                while ($row2 = @mysql_fetch_array($db_query_res2)){
                    
                    if ($row2['ent']==1) {
                        echo '<td>'.$row2[Stunde].'. Stunde: '.$row2[Statt_Lehrer].' ('.$row2[Statt_Fach].') entf&auml;llt</td></tr>';                        
                        echo '<tr>';
                    }else{
                        if ($row2['mitauf']==1) {
                            echo '<td>'.$row2[Stunde].'. Stunde: '.$row2[Sup_Lehrer].' Mitaufsicht für '.$row2[Statt_Lehrer].'</td></tr>';
                            echo '<tr>';                            
                        }else{
                            echo '<td>'.$row2[Stunde].'. Stunde: '.$row2[Sup_Lehrer].' ('.$row2[Sup_Fach].') statt '.$row2[Statt_Lehrer].' ('.$row2[Statt_Fach].')</td></tr>';
                            echo '<tr>';
                        }
                    }
                }
            }
            echo '</table>';
        }else {
            echo '&nbsp;';
        }   
        echo '</td></tr>'."\n";
        $zeit+=86400;
    }
}else{
    $zeit = time(); // Aktuelle Zeit in Sekunden
    $datum = getdate($zeit);
    $zeit=$zeit - 86400*($datum[wday]-1);
    for ($x=1;$x<7;$x++){ //Woche generieren
        if ($x%2==0){
            echo '<tr style="BACKGROUND-COLOR: #ffffff">';
        }else{
            echo '<tr style="BACKGROUND-COLOR: '.$color[cell].'">';
        }
        $datum = getdate($zeit);
        $date="$datum[year]-$datum[mon]-$datum[mday]";
        echo '<td width=75 >';
        echo $datum[weekday] . "<br>";
        echo "$datum[mday].$datum[mon].$datum[year]";
        echo '</td><td>';
        $db_query1='
            select 
                kalender.Datum as datum, 
                kalender.Zeit as zeit,
                kalender.Beschreibung as beschreibung,
                kalender.Abteilung as abteilung,
                kalender.hd as hd
            from
                kalender
            where 
                datum="'.$date.'"
                and abteilung='.$abteilung[id].'
            order by 
                kalender.datum asc';
        #echo $db_query1;
        $db_query_res1 = @mysql_db_query($db_name,$db_query1);
        $num_row1=@mysql_num_rows($db_query_res1);
        #echo $num_row1."<br>";
        if (@mysql_num_rows($db_query_res1)!=0){
            echo '<TABLE style="WIDTH: 100%" cellSpacing=0 cellPadding=0 width="100%" border=0>';
                while($row1 = mysql_fetch_array($db_query_res1)) {
                    if ($row1[hd]==0){
                        echo '<tr><td>'.$row1[zeit].'</td><td>'.$row1[beschreibung].'</td></tr>';
                    }else{
                        echo '<tr><td>'.$row1[beschreibung].'</td></tr>';
                    }
                }
            echo '</table>';
        }else{
            echo '&nbsp;';
        }
        echo '</td></tr>';
        $zeit+=86400;
    }

}
?>
</table><br>
<input type=hidden value=<? echo $abteilung['id'];?> name=abt>
<?
if (auth()==1 and $userdata['rights']=='a'or auth()==1 and $userdata['rights']=='l' and $_GET['Abteilung']==$userdata['abteilung'] and $_GET['Type']=="Lehrer"){
    echo '<table cellSpacing=0 cellPadding=0 width="770" border=0 id=TABLE2> <tr><td colspan=2><center><input type=button onclick=\'send_sup();\' value="Neue Supplierung" name="add"></center></td><td><center><input type=button onclick=\'send_term();\' value="Neuer Termin" name="term"></center></tr></table>';
}
?>
</form>
</BODY>
</HTML>
