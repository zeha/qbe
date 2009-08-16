<?
$query_start = 'select
                    min(id)
                from
                    stunde
                where
                    start between "'.$vonzeit.'" and "'.$biszeit.'" limit 1';
					
$result_start = mysql_db_query($db_name, $query_start);
$row_start = mysql_fetch_array($result_start);

$start = $row_start['min(id)'];
					
$query_ende = 'select
                    max(id)
                from
                    stunde
                where
                    start between "'.$vonzeit.'" and "'.$biszeit.'" limit 1';
					
$result_ende = mysql_db_query($db_name, $query_ende);
$row_ende = mysql_fetch_array($result_ende);

$ende = $row_ende['max(id)'];

$db_query2 = '
    select 
        klasse.Name as klasse,
        fach.Name as fach,
        stundenplan.WTag as wtag,
        stundenplan.Stunde as stunde,
        stundenplan.Klasse as klasse_id,
        stundenplan.Fach as fach_id
    from
        stundenplan
        inner join lehrer on (lehrer.id = stundenplan.Lehrer)
        inner join klasse on (klasse.id = stundenplan.Klasse)
        inner join fach on (fach.id = stundenplan.Fach)    
    where
            stundenplan.Lehrer = '.$_GET['lehrer'].'
        and
            stundenplan.WTag = '.date("w",$vontst).'
        and
            stundenplan.Stunde between ('.$start.')
            and ('.$ende.')
    order by wtag,stunde';
    //echo $db_query2."<br>";
$db_query_res = mysql_db_query($db_name,$db_query2) or die(mysql_error());
if (mysql_num_rows($db_query_res)!=0){
    while($row = mysql_fetch_array($db_query_res)) {
    echo '  <tr>
            <td style="width: 95px;">
                <input type=hidden name=datum[] value="'.date("Y-m-d",$vontst).'">
                <input type=hidden name=tag[] value='.$row['wtag'].'>
                <input type=hidden name=stunde[] value='.$row['stunde'].'>
                <input type=hidden name=klasse[] value='.$row['klasse'].'>
                <input type=hidden name=statt_fach[] value='.$row['fach_id'].'>'."\n".'
                <center>'.$wtage[$row['wtag']-1]." - ".$row['stunde'].'</center>
            </td>
            <td style="width: 95px;">
                <center>'.$row['klasse'].'</center>
            </td>
            <td style="width: 95px;">
                <center>'.$row['fach'].'</center>
            </td>';
    $db_query4 = '
        select
            fach.name as fach,
            lehrer.kz as lehrer,
            supplierung.Bemerkung as bem,
            supplierung.LfdNr as id,
            supplierung.mitauf as mit,
            supplierung.entfaellt as ent
        from
            supplierung
            inner join lehrer on (supplierung.sup_lehrer = lehrer.id)
            inner join fach on (supplierung.sup_fach = fach.id)                        
        where
                supplierung.datum = "'.date("Y-m-d",$vontst).'"
            and
                supplierung.stunde = "'.$row['stunde'].'"
            and
                supplierung.klasse = "'.$row['klasse'].'"
            and
                (supplierung.statt_lehrer = "'.$_GET['lehrer'].'"
                or supplierung.entfaellt="1")';
        //echo $db_query4;
        $db_query_res4 = mysql_db_query($db_name,$db_query4) or die (mysql_error());
        if (mysql_num_rows($db_query_res4)==0){                        
            echo '<td align=right style="width: 90px;">
                <select name=sup_lehrer[]>'."\n";
            $db_query2 = '
            select
                lehrer.kz as kz,
                stundenplan.lehrer as id
            from
                stundenplan
                inner join klasse on (klasse.id = stundenplan.klasse)
                inner join lehrer on (lehrer.id = stundenplan.lehrer)
                inner join fach on (fach.id = stundenplan.fach)
            where
                    stundenplan.stunde = '.$row['stunde'].'
                and
                    stundenplan.wtag = '.$row['wtag'].'
                and
                    stundenplan.klasse = "'.$row['klasse'].'"
                and
                    stundenplan.fach = '.$row['fach_id'];
                
            $db_query_res2 = mysql_db_query($db_name,$db_query2);
                echo '<option value=ent>- Entfällt -</option>';
            if (mysql_num_rows($db_query_res2)!=1){
                echo '<option value="">- MitAuf -</option>'."\n";
                while($row2 = mysql_fetch_array($db_query_res2)) {
                echo ($row2['id']!=$_GET['lehrer']?'<option value="'.$row2['id'].':mit">'.$row2['kz'].'</option>'."\n":"");                        
                }
            }
            $db_query2='
            select
                lehrer.kz as kz,
                stundenplan.lehrer as id
            from
                stundenplan
                inner join lehrer on (lehrer.id = stundenplan.lehrer)
            where
                stundenplan.stunde = '.$row['stunde'].'
                and
                stundenplan.wtag = '.$row['wtag'].'
                and
                stundenplan.klasse is NULL
            order by kz';
            $db_query_res2 = mysql_db_query($db_name,$db_query2);
            //$db_query_res3 = mysql_db_query($db_name,$db_query3);
            if (mysql_num_rows($db_query_res2)!=0){
                echo '<option value="">- Supl -</option>'."\n";
                while($row2 = mysql_fetch_array($db_query_res2)) {
                    $db_query3 = '
                    select
                        *
                    from
                        supplierung
                    where
                        supplierung.sup_lehrer = '.$row2['id'].'
                        and
                        supplierung.Stunde = '.$row['stunde'].'
                        and
                        supplierung.datum = "'.date("Y-m-d",$vontst).'"';
                    //echo "\n".$db_query3."\n";
                    $db_query_res3 = mysql_db_query($db_name,$db_query3);
                    if (mysql_num_rows($db_query_res3)==0) {
                        echo '<option value="'.$row2['id'].'">'.$row2['kz'].'</option>'."\n";
                    }
                }
            }
            echo '</select></td>';
            echo '<td align=left style="width: 90px;"><select name=sup_fach[]>';
        for ($y=1;$y<count($fach);$y++) {
            echo '<option value='.$y.'>'.$fach[$y].'</option>';
        }
        echo '</select></td><td colspan=2><input type=text size=60 name=bem[]></td></tr>';
    }}
}
?>