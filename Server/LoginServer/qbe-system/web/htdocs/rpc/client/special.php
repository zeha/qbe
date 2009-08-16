<?php
	if (isset($_GET['url'])) { $url = $_GET['url']; } else { $url = 'http://127.0.0.1:7666/web/menu'; }
	$today = getdate();
	$year = $today['year'];
?>
<html>
	<head>
		<title>Qbe iLogin</title>
		<style>
			body,p,input { font-family: "Trebuchet MS",sans-serif; font-size: 9pt; background-color: #004E89; color: black; margin: 0px 0px 0px 0px; }
			a { color: white; }
			a:hover { color: red; }
			body { background-image: url(../../graphics/client_bg.png); }

			.logotext {  font-size: 21pt; font-family: Trebuchet MS; font-weight:bold; }
			.logoqbeiv { color: white; }
			.logoqbe { color: #3333ff; }
			.logoprog { color: red; }
			.localpos { position: absolute; left: 5px; top: 11px; }
			li { text-indent: 20px; }
			b { font-size: 120%;}
		</style>

	</head>
	<script language="VBScript">
		On Error Resume Next	' this is required, 
			' as RegRead will fail the first time
		Dim WshSell, year, url, regval
		set WshShell = CreateObject("WScript.Shell")
		year = "<?=$year?>"
		url = "<?=$url?>"
		regkey = "HKEY_CURRENT_USER\Software\iLogin\XMasDone"
		value = WshShell.RegRead(regkey)
		if value = year then: window.location = url: end if
		WshShell.RegWrite regkey,year
	</script>
	<body>

	<div style="color: white; margin-left: 10px;">
	<img src="glocken.png" style="position: absolute; right: 10px; top: 100px;">
	<br/>
	<b>Frohe Weihnachten</b> und einen <br/>
	<b>guten Rutsch ins neue Jahr</b> w&uuml;nschen:<br/>
	<br/>
	<b>Qbe Development</b>:<br>
		<li>Andreas St&uuml;tzner
		<li>Christian Hofst&auml;dtler
	<br/>
	<br/>
	<b>HTBLuVA Wiener Neustadt</b>:<br>
		<li>Dr. Karl Filz
	<br/>
	<br/>
	<a href="<?=$url?>">Weiter...</a>
	</div>
	
	</body>
</html>
