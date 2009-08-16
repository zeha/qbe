<?
    $db_query = 'select
            supplierung.lfdnr as id,
            statt_lehrer.kz as statt_lehrer,
            statt_fach.name as statt_fach,
            sup_fach.name as sup_fach,
            klasse.name as klasse,
            DATE_FORMAT(supplierung.datum,"%d.%m.%Y") as datum,
            supplierung.stunde as stunde,
            supplierung.mitauf as mit,
            supplierung.bemerkung as bem,
            DATE_FORMAT(supplierung.check_lehrer,"%d.%m.%Y") as che
    from
            supplierung
            inner join lehrer as statt_lehrer on (statt_lehrer.id = supplierung.statt_lehrer)
            inner join fach as statt_fach on (statt_fach.id = supplierung.statt_fach)
            inner join fach as sup_fach on (sup_fach.id = supplierung.sup_fach)
            inner join klasse on (klasse.id = supplierung.klasse)
    where
            supplierung.sup_lehrer ='.$userdata['user'].'
            and
            supplierung.entfaellt <> 1
    order by datum, stunde';
    //echo $db_query."<br>";
    //print_r($userdata);
    $db_query_res = @mysql_db_query($db_name,$db_query);
    if (@mysql_num_rows($db_query_res)!=0){
        echo '<center><h2>Supplierungen</h2></center>';
    ?>
    <center><table border=0>
    <tr><th width=95>Datum</th><th width=70>Klasse</th><th width=70>Stunde</th><th width=70>Fach</th><th colspan=2 width=95>Abwesend</th><th width=200>Bemerkung</th><th width=95>Bestätigung</th>
    <?
    while ($row = @mysql_fetch_array($db_query_res)){
        echo '
        <tr>
            <td>
                <center>'.$row['datum'].'</center>
            </td>
            <td>
                <center>'.$row['klasse'].'</center>
            </td>
            <td>
                <center>'.$row['stunde'].'</center>
            </td>
            <td>
                <center>'.($row['mit']?"MitAuf":'('.$row['sup_fach'].')').'</center>
            </td>
            <td align=right>
                '.$row['statt_lehrer'].'
            </td>
            <td align=left>
                ('.$row['statt_fach'].')
            </td>
            <td>
                <center>'.$row['bem'].'</center>
            </td>
            <td>
                <center>'.($row['che']?$row['che']:'<img src="Pictures/check_down.png" onmouseover=\'this.src = "Pictures/check_up.png";\' onmouseout=\'this.src = "Pictures/check_down.png";\'  onclick=\'send_check('.$row['id'].',"l");\'>').'</center>
            </td>
        </tr>';
    }
    
?>
</table></center>
<?
    }
?>