<?
require "../../sas.inc.php";
require "version.php";

sas_start("Qbe SAS Client ".$qbe_ilogin2_current,"../../","/modules/client/",0);
sas_showmenu();

?>
	<table class=borderoff>
	<tr>
	<td valign=top>
	<img src="../../graphics/qbox.png" alt="" class="qbox">
	</td>
	<td>

	<table style="border: 1px solid black; background-color: white; color: black;" width=500 cellpadding=10><tr><td width=200>

		<b>Linux</b><br/>
		Zum Anmelden dann: <a href="http://localhost:7666/" style="color: black;">hier klicken</a>.<br>

	</td><td>

		<img src="package.png"> <a href="files/QbeClient-XPlat.tar.gz" style="color: black; font-size: 110%; font-weight: bold;">Download Now!</a><br>
	</td><td>
		<a style="color: black;" href="http://www.go-mono.com/"><img src="mono-compatible.png" border=0 alt="Compatible with mono" /></a>
	
	</td></tr></table><br/>

	<table style="border: 1px solid black; background-color: white; color: black;" width=500 cellpadding=10><tr><td width=200>

		<b>Windows (Fat Client)</b><br/>

	</td><td>

		<img src="package.png"> <a href="files/QbeSASClient-Fat.exe" style="color: black; font-size: 110%; font-weight: bold;">Download Now!</a><br>
	</td><td>
	
	</td></tr></table><br/>



	<a href="index">&lt;&lt; Windows Client</a><br>

	</blockquote>
	<br>
		
<?
	/*
	qbe_web_maketable(true);
	?><tr><th colspan=3>Development Patches:</th></tr><?
	
	$DIR = opendir(".");
	while($FILE=readdir($DIR))
	{
		if (strstr($FILE,'patch')!='')
		{
			qbe_web_maketr();
		?>
		<td class="imagecol"><img src="/graphics/icon-file.png"/></td><td><a href="<?=$FILE?>"><?=$FILE?></a></td><td><?=strftime("%d.%m.%Y %H:%m",filemtime($FILE))?></td></tr>
		<?
		}
	}
	closedir($DIR);
?>
	</table>
	
	<? */ ?>
	
	</td>
	</tr>
	</table>
	
	<br><br>
	Die Pr&auml;sentation findet sich <a href="htl-pres.pdf">hier</a> und der Flyer <a href="htl-flyer.pdf">hier</a>.<br>
	<br>
	<br>
	Fragen bitte an <a href="mailto:k.filz@htlwrn.ac.at">k.filz@htlwrn.ac.at</a>.<br>
	<br>
	<br>
	<br>
	<br>
	
	Aktuelle Verions-ID: <?=$qbe_ilogin2_current?><br/>
	Minimale Versions-ID: <?=$qbe_ilogin2_version?><br/>
<?	
sas_end();
