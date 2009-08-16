<?php
session_start();
include("include/db.inc");
include("auth.php");
include("include/sup-check.inc");
$userdata = $_SESSION['userdata'];
/*if (auth()!=1) {
    header("Location: login.php");
}*/
$abt = $_GET['abt'];
$alg = (isset($_GET['Allgemein']) ? $_GET['Allgemein'] : '');

if ($alg==1) {$abt=0;}

if ((isset($_GET['action']) ? $_GET['action'] : '')=="add") {
    $type=$_GET['type'];
    $vonarray = split("-",$_GET['von']);
    $bisarray = split("-",$_GET['bis']);
    $vonzeit = split(":",(isset($vonarray[1]) ? $vonarray[1] : ''));
    $vondatum = split("\.",$vonarray[0]);
    $biszeit = split(":",(isset($bisarray[1]) ? $bisarray[1] : ''));
    $bisdatum = split("\.",$bisarray[0]);
    $zeit = mktime((isset($biszeit['0']) ? $biszeit['0'] : ''), (isset($biszeit['1']) ? $biszeit['1'] : ''), 0, $bisdatum['1'], $bisdatum['0'], $bisdatum['2']);	
	$vontst = mktime($vonzeit[0],(isset($vonzeit[1]) ? $vonzeit[1] :''),0,(isset($vondatum[1]) ? $vondatum[1] :''),$vondatum[0],$vondatum[2])."<br>";
    $bistst = mktime($biszeit[0],(isset($biszeit[1]) ? $biszeit[1] :''),0,(isset($bisdatum[1]) ? $bisdatum[1] :''),$bisdatum[0],$bisdatum[2])."<br>";
    $vonnull = mktime(23,59,59,$vondatum[1],$vondatum[0],$vondatum[2]);
    $diff = mktime($biszeit[0],(isset($biszeit[1]) ? $biszeit[1] : ''),0,$bisdatum[1],$bisdatum[0],$bisdatum[2]) - mktime($vonzeit[0],(isset($vonzeit[1]) ? $vonzeit[1] : ''),0,$vondatum[1],$vondatum[0],$vondatum[2]);
    	
	if (bcmod($diff,24*3600)==0) {
        $diffd = bcdiv($diff,24*3600);
    }else{
        $diffd=bcdiv($diff,24*3600);
        $diffsec = bcmod($diff,24*3600);
    }
    //echo $diff." - ".$diffd." - ".$diffsec;
    if ($userdata['rights']=="a" or $userdata['rights']=="av") {
        $lehrer = $_GET['lehrer'];
    }else{
        $lehrer = $userdata['user'];
    }
    if ($diffd==0) {
        $db_query='
            Insert into
                termin(id, date, unixdate, time, text, abteilung, typ, raumnr, lehrer, cn, klasse, check_klasse)
            values
                ("",
                "'.date("Y-m-d",$vontst).'",
				'.$zeit.',
                "'.date("H:i",$vontst)." - ".date("H:i",$bistst).'",
                "'.($_GET['bem']?$_GET['bem']:"0").'",
                '.($_GET['abt']?$abt:"0").',
                '.$type.',
                "'.($_GET['raum']?$_GET['raum']:"0").'",
                "'.($_GET['lehrer']?$_GET['lehrer']:"0").'",
                '.($_GET['rechner']?$_GET['rechner']:"0").',
                "'.($_GET['klasse']?$_GET['klasse']:"0").'",
                NULL)';
        $db_query."<br>";
        $db_query_res = mysql_db_query($db_name,$db_query) or die (mysql_error());
        if (mysql_affected_rows()!=1) {
        }else{
            header("Location: cal2.php?Abteilung=".$_GET['abt']);
        }
    }else{
        //echo date("z",$vontst)." - ".date("z",$bistst)."<br>";
        $y=1;
        for ($x=date("z",$vontst);$x<date("z",$bistst)+1;$x++) {
            if ($y) {
                $time = date("H:i",$vontst);
            } else {
                $time = NULL;
            }
            $db_query='
            Insert into
                termin (id, date, unixdate, time, text, abteilung, typ, raumnr, lehrer, cn, klasse, check_klasse)
            values
                (NULL,
                "'.date("Y-m-d",$vontst).'",
				'.$zeit.',
                "'.$time.'",
                "'.($_GET['bem']?$_GET['bem']:"").'",
                "'.($_GET['abt']?$_GET['abt']:"0").'",
                "'.$type.'",
                "'.($_GET['raum']?$_GET['raum']:"0").'",
                "'.($lehrer?$lehrer:"0").'",
                "'.($_GET['rechner']?$_GET['rechner']:"0").'",
                "'.($_GET['klasse']?$_GET['klasse']:"0").'",
                NULL)';
            //echo $db_query."<br>";
            $db_query_res = mysql_db_query($db_name,$db_query);// or die(mysql_error());
            $vontst = $vontst + 86400;
            $y=0;
        }
    }
    header("Location: cal2.php?Abteilung=1");
}

?>
<html>
<head>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
<script>
function send() {
    document.getElementsByName('action')[0].value="add";
    document.getElementsByName('form1')[0].submit();
}
function cancel() {
    history.back();
}
function ch_types() {
    switch(document.form1.type.options[document.form1.type.selectedIndex].text) {
        case "Raumreservierung":
            hide_all_frm();
            document.getElementsByName("raum")[0].style.visibility = "visible";
            document.getElementsByName("raum")[1].style.visibility = "visible";
            <?
			if ($userdata['rights']=="a" or $userdata['rights']=="av") {
            echo '
            document.getElementsByName("lehrer")[0].style.visibility = "visible";
            document.getElementsByName("lehrer")[1].style.visibility = "visible";';
            }
			?>
            break;
        case "Computerreservierung":
            hide_all_frm();
            document.getElementsByName("rechner")[0].style.visibility = "visible";
            document.getElementsByName("rechner")[1].style.visibility = "visible";
            <?
			if ($userdata['rights']=="a" or $userdata['rights']=="av") {
            echo '
            document.getElementsByName("lehrer")[0].style.visibility = "visible";
            document.getElementsByName("lehrer")[1].style.visibility = "visible";';
            }
			?>
            break;
        case "Sonstiges":
            hide_all_frm();
            <?
			if ($userdata['rights']=="a" or $userdata['rights']=="av") {
            echo '
            document.getElementsByName("lehrer")[0].style.visibility = "visible";
            document.getElementsByName("lehrer")[1].style.visibility = "visible";';
             }
			 ?>
            document.getElementsByName("klasse")[0].style.visibility = "visible";
            document.getElementsByName("klasse")[1].style.visibility = "visible";
            break;
        case "SMÜ":
            hide_all_frm();
            document.getElementsByName("klasse")[0].style.visibility = "visible";
            document.getElementsByName("klasse")[1].style.visibility = "visible";
            <?
			if ($userdata['rights']=="a" or $userdata['rights']=="av") {
            echo '
            document.getElementsByName("lehrer")[0].style.visibility = "visible";
            document.getElementsByName("lehrer")[1].style.visibility = "visible";';
             }
			 ?>
            break;
        case "Schularbeit":
            hide_all_frm();
            document.getElementsByName("klasse")[0].style.visibility = "visible";
            document.getElementsByName("klasse")[1].style.visibility = "visible";
            <?
			if ($userdata['rights']=="a" or $userdata['rights']=="av") {
            echo '
            document.getElementsByName("lehrer")[0].style.visibility = "visible";
            document.getElementsByName("lehrer")[1].style.visibility = "visible";';
            }
			?>
            break;
        case "Test":
            hide_all_frm();
            document.getElementsByName("klasse")[0].style.visibility = "visible";
            document.getElementsByName("klasse")[1].style.visibility = "visible";
            <?
			if ($userdata['rights']=="a" or $userdata['rights']=="av") {
            echo '
            document.getElementsByName("lehrer")[0].style.visibility = "visible";
            document.getElementsByName("lehrer")[1].style.visibility = "visible";';
            }
			?>
            break;
        case "Matura":
            hide_all_frm();
            document.getElementsByName("raum")[0].style.visibility = "visible";
            document.getElementsByName("raum")[1].style.visibility = "visible";
            break;
        default:
            hide_all_frm();
            break;
    }
}
function hide_all_frm() {
    document.getElementsByName("raum")[0].style.visibility = "hidden";
    document.getElementsByName("raum")[1].style.visibility = "hidden";
    document.getElementsByName("lehrer")[0].style.visibility = "hidden";
    document.getElementsByName("lehrer")[1].style.visibility = "hidden";
    document.getElementsByName("klasse")[0].style.visibility = "hidden";
    document.getElementsByName("klasse")[1].style.visibility = "hidden";
    document.getElementsByName("rechner")[0].style.visibility = "hidden";
    document.getElementsByName("rechner")[1].style.visibility = "hidden";
}
</script>
</head>
<body onclick="hide_all()">
<? if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}
?>
<form action="add_term.php" name=form1>
<table border=0>
<tr>
    <td align=right>Type: </td>
    <td><select name=type onchange="ch_types();">
    <?
        $db_query='select * from kat where showit=1';
        $db_query_res = @mysql_db_query($db_name,$db_query);
        if (@mysql_num_rows($db_query_res)!=0){
        while($row = @mysql_fetch_array($db_query_res)) {
            echo '<option value='.$row['id'].'>'.$row['Bezeichnung'].'</option>';
        }
        }
    ?>
    </select></td>
    <td></td>
    <td align=right>Allgemein: </td>
    <td><input type="checkbox" name="Allgemein" value=1></td>
</tr>
<tr>
    <td align=right>Von: </td>
    <td><input name=von type=text width=10 value=<?=(isset($_GET['von'])?$_GET['von']:date("d.m.Y - H:m"));?>></td>
    <td></td>
    <td name=lehrer style="visibility: hidden;" align=right>Lehrer: </td>
    <td name=lehrer style="visibility: hidden;">
        <select name=lehrer>
        <?
            $db_query='select * from lehrer order by kz';
            $db_query_res = @mysql_db_query($db_name,$db_query);
            if (@mysql_num_rows($db_query_res)!=0){
                while($row = @mysql_fetch_array($db_query_res)) {
                    echo '<option value='.$row['id'].'>'.$row['KZ'].'</option>';
                }
            }
        ?>
        </select>
    </td>
</tr>
<tr>
    <td align=right>Bis:</td>
    <td><input name=bis type=text width=10 value=<?=(isset($_GET['bis'])?$_GET['bis']:date("d.m.Y \- H\:m"));?>></td>
    <td></td>
    <td name=raum style="visibility: hidden;" align=right>Raum: </td>
    <td name=raum style="visibility: hidden;"><input name=raum type=text width=10></td>
</tr>
<tr>
    <td name=klasse style="visibility: hidden;" align=right>Klasse: </td>
    <td name=klasse style="visibility: hidden;">
        <select name=klasse>
        <?php

    $ds = ldap_connect("10.0.2.100");
    $r = ldap_list($ds,"ou=Classes,ou=People,o=htlwrn,c=at","l=".$userdata['abteil']);
    $sinfo = ldap_get_entries($ds,$r);
	array_multisort($sinfo, SORT_ASC);
for ($i = 0; $i < $sinfo["count"]; $i++)
	{
	echo '<option value='.$sinfo[$i]["ou"][0].'>'.$sinfo[$i]["ou"][0].'</option>';
	}
	ldap_close($ds);

?>
        ?>
        </select>
    </td>
    <td></td>
    <td name=rechner style="visibility: hidden;" align=right>Rechner Name: </td>
    <td name=rechner style="visibility: hidden;"><input name=rechner type=text width=10></td>
</tr>
<tr>
    <td align=right>Bemerkung: </td>
    <td colspan=3><textarea name=bem cols="30" rows="3"></textarea>
</tr>
<tr>
</tr>
<tr>
    <td></td>
    <td><center><input type=button value="Eintragen" onclick="send();"></center></td>
    <td></td>
    <td><center><input type=button value="Abbrechen" onclick="cancel();"></center></td>
    <td></td>
</tr>
<input type=hidden name=abt value="<?=$_GET['abt']?>">
<input type=hidden name=action id=action value="">
<input type=hidden name=mon id=mon value="">
</table>
</form>
</body>
</html>