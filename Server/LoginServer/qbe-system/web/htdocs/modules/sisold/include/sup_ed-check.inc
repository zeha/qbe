<?php
function lehrerfrei($datum,$stunde,$lehrer,$wotag,$ent, $id){
    global $db_name;
    $db_query='select * from supplierung where stunde='.$stunde.' and sup_lehrer='.$lehrer.' and Datum="'.$datum.'" and lfdnr!='.$id;
    //echo $db_query;
    $db_query_res=mysql_db_query($db_name,$db_query);
    if (mysql_num_rows($db_query_res)!=0) 
    {
        $lf1=0;
    }else{
        $lf1=1;
    }
    
    switch ($wotag) {
    case "MO":
        $wotag=1;
        break;
    case "Di":
        $wotag=2;
        break;
    case "MI":
        $wotag=3;
        break;
    case "DO":
        $wotag=4;
        break;
    case "FR":
        $wotag=5;
        break;
    case "SA":
        $wotag=6;
        break;
    }
    if ($ent==1) {
        return $lf1;
    }else{
    $db_query='select * from stundenplan where stunde='.$stunde.' and lehrer='.$lehrer.' and wtag="'.$wotag.'"';
    echo $db_query;
    $db_query_res=mysql_db_query($db_name,$db_query);
    if (mysql_num_rows($db_query_res)!=0)
    {
        if ($lf1==0) {
            return 0;
        }
    }else{
        if ($lf1==1) {
            return 1;
        }
    }
    }
}

function supplierungfrei($datum, $stunde, $klasse, $id) {
    global $db_name;
    $db_query='select * from supplierung where stunde='.$stunde.' and klasse='.$klasse.' and Datum="'.$datum.'" and lfdnr!='.$id;
    $db_query_res=mysql_db_query($db_name,$db_query);
    if (mysql_num_rows($db_query_res)!=0) 
    {
        return 0;
    }else{
        return 1;
    }
}
?>