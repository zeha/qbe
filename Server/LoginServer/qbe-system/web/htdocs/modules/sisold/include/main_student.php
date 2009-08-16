<?
    $db_query = 'select
            supplierung.LfdNr as id,
            statt_lehrer.KZ as statt_lehrer,
            statt_fach.Name as statt_fach,
            supplierung.Sup_Lehrer as sup_lehrer,
            sup_fach.Name as sup_fach,
            supplierung.Klasse as klasse,
            DATE_FORMAT(supplierung.Datum,"%d.%m.%Y") as datum,
            supplierung.Stunde as stunde,
            supplierung.Entfaellt as ent,
            supplierung.MitAuf as mit,
            supplierung.Bemerkung as bem,
            DATE_FORMAT(supplierung.check_klasse,"%d.%m.%Y") as che
    from
            supplierung
			inner join lehrer as statt_lehrer on (statt_lehrer.id = supplierung.Statt_Lehrer)
            inner join fach as statt_fach on (statt_fach.id = supplierung.Statt_Fach)
            inner join fach as sup_fach on (sup_fach.id = supplierung.Sup_Fach)
    where
            supplierung.Klasse ="'.(isset($userdata['Klasse']) ? $userdata['Klasse'] : '').'"
    order by Datum, Stunde';
    //echo $db_query."<br>";
    $db_query_res = @mysql_db_query($db_name,$db_query);// or die(mysql_error());
    if (@mysql_num_rows($db_query_res)!=0){
        echo '<center><h2>Supplierungen</h2></center>';
    ?>
    <center><table border=0>
    <tr><th width=95>Datum</th><th width=70>Stunde</th><th colspan=2 width=95>Abwesend</th><th colspan=2 width=95>Supplierung</th><th width=200>Bemerkung</th><th width=95>Bestätigung</th></tr>
    <?
    while ($row = @mysql_fetch_array($db_query_res)){
        echo '
        <tr>
            <td>
                <center>'.$row['datum'].'</center>
            </td>
            <td>
                <center>'.$row['stunde'].'</center>
            </td>
            <td  align=right>
                '.$row['statt_lehrer'].'
            </td>
            <td align=left>
                ('.$row['statt_fach'].')
            </td>';
        if ($row['ent']) {
        echo '
            <td colspan=2>
                <center>Entf&auml;llt</center>
            </td>';
        }else{
        echo '
            <td align=right>
                '.$row['sup_lehrer'].'
            </td>
            <td align=left>
                '.($row['mit']?"MitAuf":'('.$row['sup_fach'].')').'
            </td>';
        }
        echo'
            <td>
                <center>'.$row['bem'].'</center>
            </td>
            <td>
                <center>'.($row['che']?$row['che']:'<img src="Pictures/check_down.png" onmouseover=\'this.src = "Pictures/check_up.png";\' onmouseout=\'this.src = "Pictures/check_down.png";\'  onclick=\'send_check('.$row['id'].',"s");\'>').'</center>
            </td>
        </tr>';
    }
?>
    </table></center>
    <br>
<?
    }
?>
<?
$db_query = '
select
  termin.id as id,
  kat.kurzbez as typ,
  DATE_FORMAT(termin.date,"%d.%m.%Y") as datum,
  lehrer.KZ as lehrer,
  termin.text as text,
  DATE_FORMAT(termin.check_klasse,"%d.%m.%Y") as che,
  termin.time as stunde
from
    termin
	inner join lehrer on (termin.lehrer = lehrer.id)
    inner join kat on (kat.id = termin.typ)
where
    klasse = "'.(isset($userdata['Klasse']) ? $userdata['Klasse'] : '').'"';
    //echo $db_query;
    $db_query_res = @mysql_db_query($db_name,$db_query) or die (mysql_error());
    if (@mysql_num_rows($db_query_res)!=0){
        ?>
            <center><h2>Termine</h2></center>
            <center><table>
            <tr><th>Type</th><th width=70>Stunde</th><th width=95>Datum</th><th width=70>Lehrer</th><th width=200>Beschreibung</th><th width=95>Bestätigung</th></tr>
        <?
        while ($row = @mysql_fetch_array($db_query_res)){
            ?>
            <tr><td><center><?=$row['typ'];?></td><td><center><?=$row['stunde'];?></td><td><center><?=$row['datum'];?></td><td><center><?=$row['lehrer'];?></td><td><center><?=$row['text'];?></td><td><center><?=($row['che']?$row['che']:'<img src="Pictures/check_down.png" onmouseover=\'this.src = "Pictures/check_up.png";\' onmouseout=\'this.src = "Pictures/check_down.png";\'  onclick=\'send_check_termin('.$row['id'].');\'>');?></center></td></tr>
            <?
        }
    ?>
            </table></center>
    <?    
    }
?>