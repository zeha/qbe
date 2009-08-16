<?
////////////////////////////////////////////////////////////////////////////////////
// Includieren von Funktionen

include("include/db.inc");
include("auth.php");
include("include/sup-check.inc");
include("include/const.php");

// ebenso die Fächer aus der Datenbank lesen und in eine Variabel schreiben

$db_query='select * from fach';
$db_query_res = @mysql_db_query($db_name,$db_query);
if (@mysql_num_rows($db_query_res)!=0){
    while($row = @mysql_fetch_array($db_query_res)) {
        $fach[$row['id']]=$row['Name'];
    }
}

// Abfragen ob in der action Variable add ( hinzufügen einer neuen Supplierung)

if ((isset($_GET['action']) ? $_GET['action'] : '') == "add") {
        
        // Schleife um auch Supplierungen die über mehrere Tage gehen auch eintragen zu können
        
        for ($x=0;$x<count($_GET['tag']);$x++)
        {
            
        // Speicher der Werte des Fomulares in Variablen
        
            $klasse = $_GET['klasse'][$x];
            $datum = $_GET['datum'][$x];
            $abteilung = $_GET['abt'];
            $statt_lehrer = $_GET['lehrer'];
            $statt_fach = $_GET['statt_fach'][$x];
            $stunde = $_GET['stunde'][$x];
            $sup_fach = $_GET['sup_fach'][$x];
            $entf = ($_GET['sup_lehrer'][$x]=="ent"?1:0); // kleinest if schleife die es gibt: <Bedingung>?<Bedingung ist wahr>:<Bedingung ist falsch>
            $bem = $_GET['bem'][$x];
            $test = split(":",$_GET['sup_lehrer'][$x]);
            $ign = (is_numeric($_GET['sup_lehrer'][$x])?0:1);
            
            // Mitaufsicht wird in der Form '<Lehrer ID>:mit' übertragen damit es von einer Supplierung unterschieden werden kann
            
            if (count($test)==2) {
                $mitauf = ($test[1]=="mit"?1:0);
                $sup_lehrer = $test[0];
            }else{
                $sup_lehrer = ($_GET['sup_lehrer'][$x]=="ent"?1:$_GET['sup_lehrer'][$x]);
                $mitauf = 0;
            }
            
            // wenn in der Variable $sup_lehrer kein Numerischer Wert steht oder mitaufsicht gesetz ist wird
            // die variable $ign gesetzt und somit keine Eintragung in die Datenbank vorgenommen
            
            if (is_numeric($sup_lehrer)) {
                $ign=0;
            }else{
                if ($mitauf) {
                    $ign=0;
                }else{
                    $ign=1;
                }
            }
            
            // Eintragen der Daten in die Datenbank
            
            if ($ign==0) {
                $db_query='
                insert into
                    supplierung (LfdNr, Datum, unixdate, Klasse, Abteilung, Statt_Lehrer, Statt_Fach, Stunde, Sup_Lehrer, Sup_Fach, Entfaellt, MitAuf, Bemerkung, check_klasse, check_lehrer)
                values
                (NULL,
                "'.$datum.'",
				'.time().',
                "'.$klasse.'",
                '.$abteilung.',
                '.$statt_lehrer.',
                '.$statt_fach.',
                '.$stunde.',
                '.$sup_lehrer.',
                '.$sup_fach.',
                '.$entf.',
                '.$mitauf.',
                "'.$bem.'",
                NULL,
                NULL)';
                $db_query_res = @mysql_db_query($db_name,$db_query);
                if (@mysql_affected_rows()!=1) {
                    $div_err='<center>Fehler beim Eintragen der Supplierung: '.mysql_error().'</center><br>';
                }
            // wenn ein supplierung länger als einen Tag dauert
            // muss für jeden Tag ein eintrag in der Termindatenbank erfolgen
            
                if ((isset($olddate) ? $olddate : '') <$datum) {
                $db_query2 = '
                insert into
                    termin (id, date, unixdate, time, text, abteilung, typ, raumnr, lehrer, cn, klasse, check_klasse)
                values
                (NULL,
                "'.$datum.'",
				'.time().',
                0,
                "Abwesend",
                '.$abteilung.',
                5,
                0,
                '.$statt_lehrer.',
                0,
		0,
		0)';
                $db_query_res2 = @mysql_db_query($db_name,$db_query2);
                if (@mysql_affected_rows()!=1) {
            // Bei einem Fehler wird eine Fehlermeldung ausgegeben
                    $div_err=$div_err + '<center>Fehler beim Eintragen der Daten: '.mysql_error().'</center>';
                }
                }
                $olddate = $datum;
            }
        }
    }
?>
<HTML>
<HEAD>
<TITLE></TITLE>
<link rel=stylesheet href=main.css>
<script language=JavaScript src=main.js>
</script>
<script>
////////////////////////////////////////////////////////////////////////////////////
// send_view()
// Funktion:
//           -) ändert das Value der variable action
//           -) schickt das Formular ab

function send_view() {
    document.getElementsByName("action")[0].value="view";
    document.getElementById('form1').submit();
}

////////////////////////////////////////////////////////////////////////////////////
// send_add()
// Funktion:
//           -) ändert das Value der variable action
//           -) schickt das Formular ab

function send_add() {
    document.getElementsByName("action")[0].value="add";
    document.getElementById('form1').submit();
}

////////////////////////////////////////////////////////////////////////////////////
// send_del()
// Funktion:
//           -) fragt nach ob man den eintrag wirklich löschen will
//           -) ruft die Datei del_sup.php mit dem Parameter id in einem eigenen Fenster auf
1
function send_del(id) {
    del = confirm("Eintrag löschen");
    if (del) {
        window.open("del_sup.php?id="+id,"Fenster1","width=1,height=1,left=0,top=0");
    }
}
</script>
</HEAD>
<BODY onclick=hide_all()>
<?

// Funktion auth() checked ob ein user angemeldet ist 
if (auth()==1){
    include("include/admin_menu.inc");
}else{
    include("include/menu.inc");
}

?>
<H1><center>Neue Supplierung</center></h1>
<form action="add_sup.php" id=form1>
<TABLE cellSpacing=1 cellPadding=1 width="75%" border=0>  
  <TR>
    <TD>Lehrer: </td>
    <td><SELECT id=lehrer style="WIDTH: 70px" name=lehrer>
    <?
        // Abfragen der Lehrer aus der Datenbank und zusammenbauen zu einem Dropdownfeld
        $db_query='select * from lehrer order by KZ';
        $db_query_res = @mysql_db_query($db_name,$db_query);
        if (@mysql_num_rows($db_query_res)!=0){
            while($row = @mysql_fetch_array($db_query_res)) {
                echo '<OPTION value="'.$row['id'].'" '.($_GET['lehrer']==$row['id']?"selected":"").'>'.$row['KZ'].'</OPTION>';
            }
        }
    ?> 
    </SELECT></TD>
    <td></td><td></td><td></td>
    <td><input type=button value="Anzeigen" onclick="send_view();">
    </td>
  </tr>
    <td>Von: </td>
    <td><input name=von type=text value=<?=(isset($_GET['von'])?$_GET['von']:date("d.m.Y"));?>></td>
    <td>Bis: </td>
    <td><input name=bis type=text value=<?=(isset($_GET['bis'])?$_GET['bis']:date("d.m.Y"));?>></td>
    <td></td>
    <td><input type=button value="Zurück"></td>
  <tr>
    </TR>
    <tr><td></td></tr>
</TABLE>
    <input type=hidden name=action id=action value="<?=$_GET['action']?>">
    <input type=hidden name=abt id=abt value="<?=$_GET['abt']?>">
    <input type=hidden name=mon id=mon value="">

<table border=0>
<?
    echo '<tr><td>'.(isset($div_err) ? $div_err : '').'</td></tr>';
?>
</table>
<hr>
<table width=100%>
<?
    
    if ((isset($_GET['action']) ? $_GET['action'] : '')=="view") {
        
        // Es wurde ein Lehrer und eine Zeit ausgewählt und auf Anzeigen geklick
        // Kopf der Tabelle
        
        echo '<tr><th>Stunde</th><th>Klasse</th><th>Fach</th><th colspan=2>Supplierung</th><th>Bemerkung</th><th></th></tr>';
        
        // Splitten der Daten aus dem Feld von um zu sehen ob eine Zeit mit angegeben wurde
        
        $vontemp =  split("-",$_GET['von']);
        
        // Kopntrollieren ob ein datum und zeit oder nur datum angegeben wurden
        
        if (count($vontemp)>1) {
            $vonzeit = $vontemp[1];
            $vondatum =  split("\.",$vontemp[0]);
        }else{
            $vonzeit = FALSE;
            $vondatum = split("\.",$_GET['von']);
        }
        
        // das selbe für die 2. Zeit
        
        $bistemp =  split("-",$_GET['bis']);
        
        if (count($bistemp)>1) {
            $biszeit = $bistemp[1];
            $bisdatum =  split("\.",$bistemp[0]);
        }else{
            $biszeit = FALSE;
            $bisdatum = split("\.",$_GET['bis']);
        }
        
        // aus den Textwerten ein Datum machen
        
        $vontst = mktime(0,0,0,$vondatum[1],$vondatum[0],$vondatum[2])."<br>";
        $bistst = mktime(0,0,0,$bisdatum[1],$bisdatum[0],$bisdatum[2])."<br>";
        $vontag = date("z",$vontst);
        $bistag = date("z",$bistst);
        
        // je nachdem ob die supplierung ein oder mehrer tage dauert müssen verschiedenen Files includiert werden
        
        if ($vonzeit!=FALSE and $biszeit!=FALSE) {
            include("include/sup_stunden.php");
        }else{
            include("include/sup_tage.php");
        }
        
        // ausgabe des Tabellen endes mit dem Button zum eintragen
        
        echo '<tr></tr><tr><td></td><td></td><td><input type=button value=Eintragen name=add onclick="send_add();"></td><td></td><td></td><td></td>';
    }
    
?>
</table>
</form>
</BODY>
</HTML>
