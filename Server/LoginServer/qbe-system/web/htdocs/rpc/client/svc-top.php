<?php
	$version = isset($_REQUEST['ver']) ? $_REQUEST['ver'] : '&szlig';
	require '../../../sysstate.php';
?>
<html>
	<head>
		<title>Qbe SAS Client</title>
		<style>
			body,p,input { font-family: "Trebuchet MS",sans-serif; font-size: 9pt; background-color: #336699; color: white; margin: 0px 0px 0px 0px;
				background-image: url(/graphics/client_top.png); }
			a { color: navy; }
			.sysstate_pass { color: green; }
			.sysstate_fail { color: yellow; font-weight: bold; }
			.sysstate_crit { color: red; font-weight: bold; }

			.logotext {  font-size: 21pt; font-family: Trebuchet MS; font-weight:bold; }
			.logoqbeiv { color: white; }
			.logoqbe { color: #3333ff; }
			.logoprog { color: red; }
			.localpos { position: absolute; left: 216px; top: 11px; }
		</style>

	</head>
	<body>
<!--	
	<span class="logotext localpos">
		<span class="logoprog"><?=$version?></span>
	</span>
-->	
	<!-- <img src="/graphics/serverok.png" style="position: absolute; right: 5px; top: 15px;"> -->
	<div style="position: absolute; right: 5px; bottom: 4px;"><?=sysstate('html');?></div>
	
	</body>
</html>
