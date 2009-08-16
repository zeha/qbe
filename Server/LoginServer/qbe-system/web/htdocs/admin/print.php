<?
include "../sas.inc.php";
sas_start("Drucker","../","/admin",1);
sas_showmenu();

?><br><?

$printer = (isset($_GET['printer']) ? $_GET['printer'] : "");
$driver = (isset($_GET['driver']) ? $_GET['driver'] : "");
$name = (isset($_GET['name']) ? $_GET['name'] : "");
$step = (isset($_GET['step']) ? $_GET['step'] : 0);
$nexturl = "";

	function alp($name,$location,$share,$driver)
	{	global $PHP_SELF;
		?>
	<table class="borderon" width=500>
	<tr>
	<td><img src="/graphics/printer.gif"></td>
	<td>
		<table width=300 align=left>
		<tr align=left>
		<td width=50>Name:</td><td><?=$name?></td>
		</tr>
		<tr>
		<td>Platz:</td><td><?=$location?></td>
		</tr>
<!--		<tr>
		<td>Freigabe:</td><td><?=$share?></td>
		</tr>-->
		</table>
	</td>
	<td valign=bottom>
		<a href="<?=$PHP_SELF?>?printer=<?=urlencode($share)?>&driver=<?=urlencode($driver)?>&name=<?=urlencode($name)?>&step=1">Installation!</a>
	</td>
	</tr>
	</table>
		<?
	}
function Xurlencode($str)
{
	return str_replace("+","%20",urlencode($str));
}

if ($step != 0)
{
	$name = str_replace("\\\\","\\",$name);
	$printer = str_replace("\\\\","\\",$printer);
	$driver = str_replace("\\\\","\\",$driver);
	$nexturl = $PHP_SELF."?name=".Xurlencode($name)."&printer=".Xurlencode($printer)."&driver=".Xurlencode($driver)."&step=".($step+1);
}

if ($step == 1)
{
/*	?><meta http-equiv="refresh" content="50; URL=<?=$nexturl?>"><?*/
	?>

	<table class="borderon" style="width: 400px; height: 300px;">
	<tr>
		<td style="background-color: LightGrey;" height="20px;">
		Schritt 1: <b>Preflight Check</b>
		</td>
	</tr>
	<tr>
		<td valign=top bgcolor=white>
		<br>

<!--	Client: <?=$sas_client_ip?><br> 
	<br>-->
	<!-- Bitte geben Sie dem System 30 Sekunden Zeit.<br> -->
	<b>Bitte melden Sie sich jetzt an der HTL-E an!</b>
	<br>
		</td>
	</tr>
	<tr>
		<td style="height: 40px; background-color: LightGrey;">
	<div align=right>
	<a style="color: black; border: 1px solid black; background-color: white;" href="<?=$nexturl?>">Weiter</a>
	</div>
		</td>
	</tr>
	</table>
	<?	

$url = Xurlencode('cmd /c net use \\\\htl-e /USER:printing print');
$exec = "perl /sas/sbin/client_exec.pl ".$sas_client_ip." ".$url;
$exec = escapeshellcmd($exec);
$response = exec($exec);

/*echo "<pre>";
echo $exec."\n";
echo $response."\n";
echo "</pre>";*/

}

if ($step == 2)
{
	?><meta http-equiv="refresh" content="50; URL=<?=$nexturl?>">

	<table class="borderon" style="width: 400px; height: 300px;">
	<tr>
		<td style="background-color: LightGrey;" height="20px;">
		Schritt <?=$step?>: <b>Treiberinstallation</b></td>
	</tr>
	<tr>
		<td valign=top bgcolor=white>
		<br>

	Treiber: <?=$name?><br>
	Client: <?=$sas_client_ip?><br>
	<br>
	Bitte geben Sie dem System 30 Sekunden Zeit.
		</td>
	</tr>
	<tr>
		<td style="height: 40px; background-color: LightGrey;">
	<div align=right>
	<a style="color: black; border: 1px solid black; background-color: white;" href="<?=$nexturl?>">Weiter</a>
	</div>
		</td>
	</tr>
	</table>
	<?	

$url = Xurlencode('cmd /c rundll32 printui.dll,PrintUIEntry /ia /f '.$driver.' /m "'.$name.'"');
$exec = "perl /sas/sbin/client_exec.pl ".$sas_client_ip." ".$url;
$exec = escapeshellcmd($exec);
$response = exec($exec);
/*
echo "<pre>";
echo $exec."\n";
echo $response."\n";
echo "</pre>";
*/
}

if ($step == 3)
{
	?>
	<table class="borderon" style="width: 400px; height: 300px;">
	<tr>
		<td style="background-color: LightGrey;" height="20px;">
		Schritt <?=$step?>: <b>Druckerinstallation</b>
		</td>
	</tr>
	<tr>
		<td valign=top bgcolor=white>
		<br>

	Drucker: <?=$name?><br>
	Client: <?=$sas_client_ip?><br>
	<br>
	Bitte geben Sie Ihrem System 30-60 Sekunden Zeit um den Drucker zu finden.
		</td>
	</tr>
	<tr>
		<td style="height: 40px; background-color: LightGrey;">
	<div align=right>
	<a style="color: black; border: 1px solid black; background-color: white;" href="<?=$PHP_SELF?>">Fertig</a>
	</div>
		</td>
	</tr>
	</table>

	<?

	echo "Printer: Installing ".$printer." on ".$sas_client_ip."...<br>\n";
	$exec = "perl /sas/sbin/client_addprinter.pl ".$sas_client_ip." ".$printer;
	echo "<pre>";
	echo $exec."\n";
	$exec = escapeshellcmd($exec);
	echo $exec."\n";
	echo "</pre>";
	system($exec);

}

if ($step == 0)
{
	alp("Lexmark Optra R Plus Series","Trakt Elektrotechnik", "\\\\htl-e\\et-lexmark","%windir%\\inf\\ntprint.inf");
	alp("Oce 3134 PCL5e","EDVO Neubau", "\\\\htl-e\\Oce-Digitalkopierer-2-Stock-EDVO", "\\\\htl-e\\software\\treiber\\drucker\\oce\\31x5UPDPCL.inf");
	alp("HP LaserJet 1100 (MS)","Saal IT1 / K16", "\\\\htl-e\\HP-E-IT-K16", "%windir%\\inf\\ntprint.inf");
}

sas_end();
?>
