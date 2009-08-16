<?
include "../sas.inc.php";
sas_start("Account Aktivieren","../","/admin",1);

if (isset($_POST['pass']))	{ $pass=$_POST['pass']; } else { $pass = ""; }
if (isset($_POST['newpass']))	{ $newpass=$_POST['newpass']; } else { $newpass = ""; }
if (isset($_POST['newpass']))	{ $newpass2=$_POST['newpass2']; } else { $newpass2 = ""; }
if (isset($_POST['accept']))	{ $accept=intval($_POST['accept']); } else { $accept = 0; }
$user=$_SESSION['user'];

if ($user == "")
	$pass = "";

if ($newpass == "")
	$pass = "";
if ($newpass2 == "")
	$pass = "";

function updateInetStatus($user)
{	error_reporting(15);
  global $sas_ldap_server, $sas_ldap_adminuser, $sas_ldap_adminpass;
  if (isset($_GET['force'])) { if ($_GET['force'] == 1) { return TRUE; }}
  $ds = ldap_connect($sas_ldap_server);
  if ($ds)
  {
        $r = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
        $list = ldap_read ( $ds, $user , "objectClass=*" );
        $entry = ldap_first_entry($ds, $list);
        $attrs = ldap_get_attributes($ds, $entry);
        $value = $attrs["inetStatus"][0];

	if ($value > 999) {
		$new = array();
		$new["inetStatus"] = $value - 1000;
		ldap_modify($ds,$user,$new);
	}

        ldap_close($ds);
  }
}
function createhomedir($user, $pass)
{
	global $sas_ldap_server;
	$ds = ldap_connect($sas_ldap_server);
	if ($ds)
	{
		ldap_bind($ds,$user,$pass);
	        $list = ldap_read ( $ds, $user , "objectClass=*");
        	$entry = ldap_first_entry($ds, $list);
	        $attrs = ldap_get_attributes($ds, $entry);
	        $homeDir = $attrs["homeDirectory"][0];
		$uidNumber = $attrs["uidNumber"][0];
		$gidNumber = $attrs["gidNumber"][0];
		$uid = $attrs["uid"][0];
	        ldap_close($ds);

		$fp = fopen("/qbe/status/activation/user/$uid","w");
		fwrite($fp,"$uid\n$homeDir");
		fclose($fp);
	}
}

if ($pass == "")
{
    ?>
	<h3><?=$user?></h3>

    <ul>
        <li>Erste Anmeldung:<br>
	Damit Sie Ihren Account in allen Systemen benutzen können, muss Ihr Account
        nun aktiviert werden. Bitte geben Sie dazu Ihr neues Passwort ein,
        und best&auml;tigen es anschlie&szlig;end.<br>
	<li>Passwortsynchronisation:<br>
	Ihr Passwort muss vom alten auf das neue System migriert werden. Dazu geben Sie bitte unten Ihr altes Passwort sowie ein neues Passwort ein. Das neue Passwort kann gleich dem alten sein.
</ul>
        <br>
        <form action="activation.php" method=post>
        <table>
        <tr><td align=right width=200>Altes Passwort:</td><td><input type="password" name="pass"></td><td></td></tr>
        <tr><td align=right>Neues Passwort:</td><td><input type="password" name="newpass"></td><td></td></tr>
        <tr><td align=right>bestätigen:</td><td><input type="password" name="newpass2"></td><td></td></tr>
	<tr><td align=right><input type="checkbox" name="accept" value="1"></td>
	<td colspan=2>
		Ich akzeptiere, dass meine Daten sowie Netzwerkaktivit&auml;ten elektronisch aufgezeichnet und ausgewertet werden.<br>
		Weiters akzeptiere ich die Nutzungsbedingungen f&uuml;r das HTL Netzwerk.</td></tr>
	<tr><td></td><td><button type="submit">Aktivieren</button></td></tr>
        </table>
        </form>
        <br><br>
        Oder: <a href="logout.php">Nicht aktivieren und Abmelden</a><br>
        <?
 } else {

 if ($accept == 1)
 {

		if ($newpass == $newpass2)
		{
			// all ok, change
			if (sas_changepassword($sas_ldap_adminuser,$sas_ldap_adminpass,$user,$newpass))
			{
				$_SESSION['pass'] = $newpass;
				$pass = $newpass; 
				createhomedir($user,$pass);
				updateInetStatus($user);
				?><br>Account wurde erfolgreich aktiviert.<br>
				<br>
				--&gt; <a href="../index.php">Weiter</a> ...<br>
				<br>
				<?
			} else {
				?>
				<span class="error">Ein Fehler ist aufgetreten.<br></span>
 				<?
 
			}
		}
		else
		{	?><span class="error">Neue Passw&ouml;rter sind nicht identisch!</span><br>
			<?	}
 } else {
 	?>
	<span class="error">Ihr Account kann nicht aktiviert werden wenn Sie den Bedingungen nicht zustimmen!</span>
	<?
 }
}

sas_end();
