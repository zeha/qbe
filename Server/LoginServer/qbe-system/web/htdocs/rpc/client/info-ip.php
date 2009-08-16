<html>
	<head>
		<title>Qbe Application Server</title>
		<style>
			body,p,input { font-family: "Trebuchet MS",sans-serif; font-size: 9pt; background-color: #336699; color: black; margin: 0px 0px 0px 0px; }
			a { color: navy; }
			body { background-image: url(../../graphics/client_bg.png); }

			.logotext {  font-size: 21pt; font-family: Trebuchet MS; font-weight:bold; }
			.logoqbeiv { color: white; }
			.logoqbe { color: #3333ff; }
			.logoprog { color: red; }
			.localpos { position: absolute; left: 5px; top: 11px; }
		</style>

	</head>
	<body>
	<?php
		$window = isset($_GET['window']) ? intval($_GET['window']) : 0;
		if ($window == 0)
		{
	?>
		
	<script language="VBScript">
		'' ***
		'' *** Special HDGuard Check
		'' *** Buggy Edition [TM]
		'' *** (C) CoPyRiGhT 2004 Christian Hofstaedtler
		'' ***
		On Error Resume Next    ' this is required,
					' as RegRead will fail the first time
		Dim WshSell, value, regkey
		set WshShell = CreateObject("WScript.Shell")
		Call Err.Clear
		value = 99
		regkey = "HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\IFWDHKNT\Start"
		value = WshShell.RegRead(regkey)
		If value = 0 Then 
			window.location = "http://127.0.0.1:7666/"
		End If
		Call Err.Clear
		value = 99
		regkey = "HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\IFWDHK2K\Start"
		value = WshShell.RegRead(regkey)
		If value = 0 Then
			window.location = "http://127.0.0.1:7666/"
		End If
	</script>

	<blockquote style="color: white;">
	<br/>
	Sie benutzen eine Legacy IP Adresse.<br/>
	Bitte konfigurieren Sie Ihren PC <a href="#" onClick="javascript:window.open('info-ip.php?window=1');">entsprechend</a> neu!<br>
	<br/>
	<?php
		$skipto = 'http://127.0.0.1:7666/web/menu';
		?>
			<a href="<?=$skipto?>">Jetzt nicht anpassen.</a><br/>
		<?
	?>
	</blockquote>

	<?php
	} else {


	?>
		<blockquote style="color: white;">
		<br/>
			<big><b>Information zur Umstellung der dynamischen IP Adressen:</b></big><br/>
			<br/>
			<ul>
			<li>Notebooks m&uuml;ssen die IP Adresse via DHCP erhalten - keine manuelle Konfiguration.<br/>
			<br/>
			<li>Bei einem Neustart von Windows sollten Notebooks die neuen 10.20.x.x IP Adressen bekommen.<br/>
			Andernfalls bitte <code>ipconfig /release</code> und anschliessend <code>ipconfig /renew</code> in der Eingabeaufforderung durchf&uuml;hren.<br/>
			</ul>
		<br/>
		<br/>
		Vielen Dank f&uuml;r Ihre Mithilfe!<br/>
		</blockquote>
	<?php
	}
	?>
	
	</body>
</html>
