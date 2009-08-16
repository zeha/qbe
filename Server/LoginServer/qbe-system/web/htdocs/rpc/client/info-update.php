<html>
	<head>
		<title>Qbe iLogin</title>
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
	Eine neue Version ist verf&uuml;gbar.<br/>
	<br/>
	<a href="../../modules/client/update.php">Download</a><br/>
	<br/>
	<?php
		$skipto = isset($_GET['skipto']) ? $_GET['skipto'] : '';
		if ($skipto != '')
		{
		?>
			<a href="<?=$skipto?>">Jetzt nicht updaten.</a><br/>
		<?
		}
	?>
	</blockquote>
	
	</body>
</html>
