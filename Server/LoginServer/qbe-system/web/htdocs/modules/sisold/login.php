<?php
// starten der Session damit die Session Daten zugänglich sind

session_start();
require("/qbe/web/htdocs/sas.inc.php");
error_reporting(15);

/////////////////////////////////////////////////////////////////////////////////////
// Include der Headerfiles für die Funktionen 

include("./include/ibutton.php");
include("./include/ldap.php");
include("./include/db.inc");
$userdata = isset($_SESSION['userdata']) ? $_SESSION['userdata'] : array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());
////////////////////////////////////////////////////////////////////////////////////
// Erzeugt den Login HTML Code
// wird aufgerufen sobalt der User auf den Punkt Login klickt

function print_login(){
    echo '
        <HTML>
        <HEAD>
        <TITLE></TITLE>
        <link rel=stylesheet href=main.css>
        <link rel=stylesheet href=login.css>
        <script language=JavaScript src=main.js>
        </script>
        <script>
            jump_status("status.php");
        </script>
        </HEAD>
        <BODY onclick=hide_all()>
    ';
    
    //Das Menu MUSS erst nach dem der Body startet importiert werden da es sonst nicht funktioniert
    
    include("include/menu.inc");
    
    echo '
        <form  action=login.php  method=post>
        <center>
        <TABLE cellSpacing=0 cellPadding=0 width="250" align=center class=login border=0>  
        <TR>
            <TD width=40><b>Benutzername:</b></TD>
            <TD><INPUT type="text" name=user style="width: 100;"></TD></TR>
        <TR>
            <TD width=40><b>Password:</b></TD>
            <TD><INPUT type="password" name=pw style="width: 100;"></TD></TR>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <TR>
            <TD colspan=2><center><INPUT type="submit" value="Login"></center></TD></TR>
        </TABLE>
        <input type=hidden name=login value=1>
        </form>
        <br>
        ';//<a href="login.php?type=ibutton">Login per IButton (Linux only / Alpha Phase)</a>
        echo '</center>
        </BODY>
        </HTML>
    ';

}

////////////////////////////////////////////////////////////////////////////////////
// Erzeugt den Logout HTML Code
// wird aufgerufen wenn ein User eingeloggt ist und auf den Menu-Punkt Logout klickt

function print_logout($data){
    global $userdata,$db_name;
    
    // Mit dem Query wird der Name des angemeldeten Users ermittelt damit er auf der Seite angezeitgt wird

    // Query vorbereiten
    
    $db_query = '
    select
        name
    from
        lehrer
    where
        id = '.$userdata['user'];
        
    // Query ausführen
    
    $db_query_res = @mysql_db_query($db_name,$db_query);    
    if (@mysql_num_rows($db_query_res)!=0){
        $row = mysql_fetch_array($db_query_res);
        $user = $row['name'];
    }
    echo '
        <HTML>
        <HEAD>
        <TITLE></TITLE>
        <link rel=stylesheet href=main.css>
        <link rel=stylesheet href=login.css>
        <script language=JavaScript src=main.js>
        </script>
        </HEAD>
        <BODY onclick=hide_all()>
    ';
    
    //In diesem Fall muss das Admin menu includiert
    
    include("include/admin_menu.inc");
    echo '
        <form  action=login.php  method=post>
        <h3>Sie sind gerade mit User: '.$userdata['user'].' eingeloggt</h3>
        <input type=hidden name=logout value=1>
        <center><input type="submit" value="Logout"></center>
        </form>
        </BODY>
        </HTML>
    ';
}


////////////////////////////////////////////////////////////////////////////////////
// Main Programm
// Abfrage der POST - Variable "login" ob diese gesetzt ist

if (isset($_POST['login'])){
    // Variable ist gesetzt somit wird geprüft ob ein username und password übergeben wurde    
$uid = $_POST['user'];
$pass = $_POST['pw'];

if ($uid != "" && $pass != "") {
	
	// Funktion sas_ldap_getdn wird ausgeführt um die DN zum Benutzer zu speichern
$dn = sas_ldap_getdn($uid);

	// Überprüfen ob das Passwort zur DN passt
$check = sas_ldap_checkpassword($dn, $pass);

if ($check == TRUE) {

	// Aufsplitten der DN um Abteilung, Rechtegruppe und Klasse zu speichern

	$ds = ldap_connect("qbe-auth.htlwrn.ac.at");

	$r = ldap_bind($ds);

	$sr = ldap_search($ds, "o=htlwrn,c=at", "uid=".$uid."");

	$info = ldap_get_entries($ds, $sr);
	
	if ($info['count']==0) {
	
	header("Location: login.php");
		
	}
	
	$udn = $info[0]['dn'];

	$user = split(",", $udn);

	$abteilung = split("=", $user[1]);

	$abteil = $abteilung[1]{0};

	$rechtegruppe = split("=", $user[2]);

	$rechte = $rechtegruppe[1];

	if ($abteil == "Verwaltung") {$abteil = "*"; $rechte = "a";} else {

	$abteilquery = "Select id from abteilung where KZ = '".$abteil."' limit 1";

	$result = mysql_db_query($db_name, $abteilquery);

	$row = mysql_fetch_array($result) or die (mysql_error());

	}

	$right = "s";
	if (sas_ldap_isgroupmember("teachers",$dn)) { $right = "l"; }
	if (sas_ldap_isgroupmember("sysops",$dn)) { $right = "a"; }

		// Auslesen des Abteilungsvorstandes und der Stellvertreter und setzen der AV-Rechte

	$avquery = "Select * from av where kz = '".$uid."' limit 1";

	$avresult = mysql_db_query('sis', $avquery);

	$avnum = mysql_num_rows($avresult);

	if ($avnum != 0) {
		$avrow = mysql_fetch_array($avresult);
		$heute = getdate();
		$weekday = $heute['weekday'];
		if ($avrow['tag'] == $weekday or $avrow['tag'] == "AV") 
		{
			$right = "av";
			$abteil = $avrow['abteilung'];
		}
	}
		
	if ($uid == "e99077") {$right = "a"; $abteil="1";}

		// Auslesen der Klasse aus der LDAP-Datenbank
		
	$ds = ldap_connect("10.0.2.100");

	if ($ds) {
    
		$r=ldap_bind($ds);
   	 	$sr=ldap_search($ds,"o=htlwrn, c=at", "uid=$uid");
    	$info = ldap_get_entries($ds, $sr);
    	$klasse = $info[0]["ou"][0];
	}

		// Daten werden in $userdata gespeichert
	$userdata = array('user' => $uid, 'abteilung' => $row['id'], 'pw' => $pass, 'valid' => 1, 'rights' => $right, 'id'=>session_id(), 'login_t'=>time(), 'Klasse'=>$klasse, 'abteil'=>$abteil);
	
		// Userdata wird in der Session gespeichert
	$_SESSION['userdata'] = $userdata;

	header("Location: test1.php?status=login");} else {

		// Login schlägt fehl Session_daten werden gelöscht
	$userdata = array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());
	$_SESSION['userdata'] = $userdata;
        
		// Browser auf die Status seite setzten mit Fehlermeldung
    @header("Location: login.php");
	}} else {

		// Login schlägt fehl Session_daten werden gelöscht
	$userdata = array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());
	$_SESSION['userdata'] = $userdata;
        
    // Browser auf die Status seite setzten mit Fehlermeldung
    
	@header("Location: login.php");

	}
	
	/* $_POST[user]=="SIS-AV" and $_POST[pw]=="AV"){
        
        $userdata = array('user'=>'144', 'pw'=>$_POST['pw'], 'valid'=>1, 'rights'=>"av", 'id'=>session_id(), 'login_t'=>time(), 'abteilung'=>1, 'Klasse'=>24);
        
    // Session Daten ins Session Cokie schreiben
    
        $_SESSION['userdata'] = $userdata;
        
    // Browser auf die Status-Seite setzen
        @header("Location: test1.php?status=login");
        
    }elseif ($_POST[user]=='SIS-schueler' and $_POST[pw]=="schueler"){
        $userdata = array('user'=>'145', 'pw'=>$_POST['pw'], 'valid'=>1, 'rights'=>"s", 'id'=>session_id(), 'login_t'=>time(), 'abteilung'=>1, 'Klasse'=>24);
        
    // Session Daten ins Session Cokie schreiben
    
        $_SESSION['userdata'] = $userdata;
        
    // Browser auf die Status-Seite setzen
        header("Location: test1.php?status=login");            
        }elseif ($_POST[user]=='SIS-lehrer' and $_POST[pw]=="lehrer"){
        $userdata = array('user'=>'146', 'pw'=>$_POST['pw'], 'valid'=>1, 'rights'=>"l", 'id'=>session_id(), 'login_t'=>time(), 'abteilung'=>1, 'Klasse'=>24);
        
    // Session Daten ins Session Cokie schreiben
    
        $_SESSION['userdata'] = $userdata;
        
    // Browser auf die Status-Seite setzen
        header("Location: test1.php?status=login");            
        }else{
    // Login schlägt fehl Session_daten werden gelöscht
        $userdata = array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());
        $_SESSION['userdata'] = $userdata;
        
    // Browser auf die Status seite setzten mit Fehlermeldung
    
        @header("Location: http://10.1.100.4/login.php");
    }*/
	
}else{
    // Abfragen ob 
    $userdata = isset($_SESSION['userdata']) ? $_SESSION['userdata'] : array('user'=>FALSE, 'pw'=>FALSE, 'valid'=>0, 'rights'=>FALSE, 'id'=>session_id());
    
    // check ob die daten die in der Session Variable sind gültig sind
    
    if ($userdata['valid']==1){
        
    // Session Daten sind ok
    
        if (isset($_POST['logout']) || isset($_GET['logout'])){
        // wenn logout gesetzt wurde werden die Daten in der Session zerstört
        // und function print_login wird aufgerufen
            @session_unregister('userdata');
            print_login();
        }else{
        // user hat im menu auf logout geglickt und bekommt die logout-site bresentiert
            print_logout($userdata);
        } 
    }else{
        // Userdaten in der Session sind nicht gültig
        if ((isset($_GET['type']) ? $_GET['type'] : '')=="ibutton"){
            
        // Punkt I-Button Login wurde angewählt
        // Function get_ibutton steht in der File ibutton.php
        // $_SERVER['REMOTE_ADDR']
        
            $uniqe_key = get_ibutton("127.0.0.1",13500);
            if ($uniqe_key == "D60000080458A101")  {
                $userdata = array('user'=>144, 'pw'=>$_POST['pw'], 'valid'=>1, 'rights'=>"av", 'id'=>session_id(), 'login_t'=>time(), 'abteilung'=>1, 'Klasse'=>54, 'ibutton'=>$uniqe_key);
                // Session Daten ins Session Cokie schreiben
                $_SESSION['userdata'] = $userdata;
                header("Location: test1.php?status=ibutton");
            }else {
                header("Location: test1.php?err=ibutton");
            }
        }else{
            
        // normales Login wurde angefordert
        
            print_login();;
        }
    }
}
?>
