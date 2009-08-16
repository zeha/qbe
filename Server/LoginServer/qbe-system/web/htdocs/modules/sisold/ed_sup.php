<?
include("include/db.inc");
include("auth.php");
include("include/sup_ed-check.inc");

$db_query='select * from lehrer';
$db_query_res = @mysql_db_query($db_name,$db_query);
if (@mysql_num_rows($db_query_res)!=0){
    while($row = @mysql_fetch_array($db_query_res)) {
        $lehrer[$row['id']]=$row['KZ'];
    }
}

$db_query='select * from fach';
$db_query_res = @mysql_db_query($db_name,$db_query);
if (@mysql_num_rows($db_query_res)!=0){
    while($row = @mysql_fetch_array($db_query_res)) {
        $fach[$row['id']]=$row['Name'];
    }
}

$db_query = 'select * from supplierung where LfdNr = '.$_GET['id'];
$db_query_res = @mysql_db_query($db_name,$db_query);

if ($_GET['action']=="edit") {
    if(!lehrerfrei($_GET['datum'],$_GET['stunde'],$_GET['sup_lehrer'],$_GET['mon'],$_GET['ent'],$_GET['id']) or !supplierungfrei($_GET['datum'],$_GET['stunde'],$_GET['klasse'],$_GET['id'])){
        $div_err='<center>Lehrer oder Klasse schon eingeplant</center>';
    }else{
        $db_query='
        update
            supplierung
        SET
        Datum = "'.$_GET['datum'].'",
        Klasse = '.$_GET['klasse'].',
        Abteilung = '.$_GET['abt'].',
        Statt_Lehrer = '.$_GET['statt_lehrer'].',
        Statt_Fach = '.$_GET['statt_fach'].',
        Stunde = '.$_GET['stunde'].',
        Sup_Lehrer = '.$_GET['sup_lehrer'].',
        Sup_Fach = '.$_GET['sup_fach'].',
        Entfaellt = '.($_GET['ent']?"1":"0").',
        chUser = '.($userdata['user']?$userdata['user']:"0").',
        chDate = "'.date("Y-m-d").'"
        where
        lfdnr = '.$_GET['id'];
        //echo $db_query;
        $db_query_res = @mysql_db_query($db_name,$db_query);
        if (@mysql_affected_rows()!=1) {
            $div_err='Fehler beim Eintragen der Supplierung';
        }else{
            header("Location: cal.php?Abteilung=".$_GET['abt']);
        }
    }
}

if (@mysql_num_rows($db_query_res)!=0){
    while($row = @mysql_fetch_array($db_query_res)) {
        $datum=split('-',$row['Datum']);
        $datum = $datum[2].'.'.$datum[1].'.'.$datum[0];       
?>
<HTML>
<HEAD>
<TITLE></TITLE>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
<script>
function send(action) {
    var datum = document.getElementById('datum').value;
    var test = datum.split(' ');
    document.getElementById('mon').value=test[0];
    test = test[1].split('.');
    var dat = new Date(test[2],test[1],test[0]);
    var output = dat.getFullYear()+'-'+dat.getMonth()+'-'+dat.getDate();
    document.getElementById('datum').value=output;
    document.getElementById('action').value=action;
    document.getElementById('form1').submit();
}
</script>
</HEAD>
<BODY onclick=hide_all() onload='formatdate("<?=$datum?>","datum",1);'>
<?
    include("include/admin_menu.inc");
?>
<H1><center>Supplierung editiern</center></h1>
<form action="ed_sup.php" methode="POST" id=form1>
<TABLE cellSpacing=1 cellPadding=1 width="75%" border=0>  
  <TR>
    <TD></TD>
    <TD>Datum: <INPUT onBlur='formatdate(this.value,this.id,1);' onClick='formatdate(this.value,this.id,0);' id=datum style="WIDTH: 110px; HEIGHT: 22px" size=9 name=datum></TD>
    <TD>Klasse: <SELECT id=statt_lehrer style="WIDTH: 70px" name=klasse>
    <?
        $db_query2='select * from klasse where Abteilung='.$row['Abteilung'];
        $db_query_res2 = @mysql_db_query($db_name,$db_query2);
        if (@mysql_num_rows($db_query_res2)!=0){
        while($row2 = mysql_fetch_array($db_query_res2)) {
            echo '<OPTION value="'.$row2['id'].'" '.($row['Klasse']==$row2['id']?"selected":"").'>'.$row2['Name'].'</OPTION>';
        }
        }
    ?> 
    </SELECT></TD>
    <TD>Stunde: <SELECT id=stunde style="WIDTH: 45px" name=stunde>
    <?
        for ($x=1;$x<=17;$x++){
            echo '<option value="'.$x.'" '.($row['Stunde']==$x?"selected":"").'>'.$x.'</option>';
        }
    ?>
    </SELECT></TD></TR>
  <tr>
  <td>
  </td>
  </tr>
  <TR>
    <TD>Statt: </TD>
    <TD>Lehrer: <SELECT id=statt_lehrer style="WIDTH: 70px" name=statt_lehrer>
    <?
        for ($x=1;$x<=sizeof($lehrer);$x++){
            echo '<OPTION value="'.$x.'" '.($row['Statt_Lehrer']==$x?"selected":"").'>'.$lehrer[$x].'</OPTION>';
        }
    ?> 
    </SELECT></TD>
    <TD>Fach: <SELECT id=statt_fach style="WIDTH: 92px" name=statt_fach>
        <?
        for ($x=1;$x<=sizeof($fach);$x++){
            echo '<OPTION value="'.$x.'" '.($row['Statt_Fach']==$x?"selected":"").'>'.$fach[$x].'</OPTION>';
        }
    ?> 
    </SELECT></TD>
    <TD rowspan=2><INPUT id=ent onclick="test(this)" style="WIDTH: 25px; HEIGHT: 20px" type=checkbox size=25 name=ent value=1 <?=($row['Entfaellt']=="1"?"checked":" ")?>>Entf&auml;llt</TD></TR>
  <TR id=sup_tr style="visibility: <?=($row['Entfaellt']=="1"?"hidden":"visible")?>;">
    <TD>Supplierung: </TD>
    <TD>Lehrer: <SELECT id=sup_lehrer style="WIDTH: 70px" name=sup_lehrer> 
    <?
        for ($x=1;$x<=sizeof($lehrer);$x++){
            echo '<OPTION value="'.$x.'" '.($row['Sup_Lehrer']==$x?"selected":"").'>'.$lehrer[$x].'</OPTION>';
        }
    ?> 
    </SELECT></TD>
    <TD>Fach: <SELECT id=sup_fach style="WIDTH: 92px" name=sup_fach>
        <?
        for ($x=1;$x<=sizeof($fach);$x++){
            echo '<OPTION value="'.$x.'" '.($row['Sup_Fach']==$x?"selected":"").'>'.$fach[$x].'</OPTION>';
        }
    ?> 
    </SELECT></TD>
    </TR>
    <tr><td></td></tr>
    <tr><td></td><td colspan=2><center><input type=button value="&Auml;ndern" onclick='send("edit");' id=add name=add></center></td></tr>
    </TABLE>
    <input type=hidden name=action id=action value="add">
    <input type=hidden name=abt id=abt value="<?=$row['Abteilung']?>">
    <input type=hidden name=mon id=mon value="">
    <input type=hidden name=id id=id value=<?=$_GET['id']?>>
    </form>
    <table border=0>
<?
    echo '<tr><td>'.$div_err.'</td></tr>';
?>
    </table>
<?     }
    }?>