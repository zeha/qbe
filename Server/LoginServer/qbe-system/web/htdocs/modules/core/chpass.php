<?
include "../../sas.inc.php";
sas_start("Passwort Ändern","../../","/modules/core",1);
sas_showmenu();

if (isset($_POST['pass']))
	$pass=$_POST['pass']; else $pass = "";
if (isset($_POST['newpass']))
	$newpass=$_POST['newpass']; else $newpass = "";
if (isset($_POST['newpass2']))
	$newpass2=$_POST['newpass2']; else $newpass2 = "";

$user=$_SESSION['user'];

if ($user == "")
	$pass = "";

if ($newpass == "")
	$pass = "";
if ($newpass2 == "")
	$pass = "";

if ($pass == "")
{?>
<form action="<?=$PHP_SELF?>" method=post>
<table>
<tr><td>Username:</td> <td><?=$_SESSION['user']?><br></td></tr>
<tr><td>Altes Passwort:</td> <td><input type="password" name="pass"><br></td></tr>
<tr><td>Neues Passwort:</td> <td><input type="password" name="newpass"><br></td></tr>
<tr><td>best&auml;tigen:</td> <td><input type="password" name="newpass2"><br></td></tr>
<tr><td></td><td><button type="submit">&Auml;ndern</button></td></tr>
</table>
</form>
<br><br>
Das neue Passwort muss mindestens 5 Zeichen lang sein!<br>
Jeder ist f&uuml;r seine Daten und Passw&ouml;rter selbst verantwortlich!
(&Auml;nderung des Passworts auf Anfrage durch den Administrator nur mit
Lichtbildausweis und gegen 5 EUR f&uuml;r Verwaltungsaufwand.)<br>


<br>
<?} else {

  if (sas_ldap_checkpassword($user,$pass))
  {
	if (($newpass == $newpass2) && (strlen($newpass) > 5))
	{
		// all ok, change
		if (sas_changepassword($user,$pass,$user,$newpass))
		{
			qbe_log_text("qbe-appmodule-core-chpass",LOG_NOTICE,"Password changed for $userid by self.");
			?> Passwort ge&auml;ndert!<br>
			<?
			$pass = $newpass;
			session_register($pass);
		} else {
			qbe_log_text("qbe-appmodule-core-chpass",LOG_NOTICE,"Password change for $userid by self failed.");
			?>
	<span class="error">Ein Fehler ist aufgetreten.<br></span>
 			<?
 
			}
		}
		else
		{
		?><span class="error">Neue Passw&ouml;rter sind nicht identisch, oder zu kurz (&gt; 5 Zeichen)!</span><br>
			<?	}
  } else {
  	?>
 	<span class="error">Ein Fehler ist aufgetreten.</span><br>
	<ul>
	<li>Altes Passwort OK?<br>
	<li>Neues Passwort lang genug? (&gt; 5 Zeichen?)<br>
	</ul>
	
	<?
  }

}

sas_end();
?>
