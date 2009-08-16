<?
	require('../../sas.inc.php');
	qbe_error_handler_on();

	?>
	<html>
	<head>
		<title>Qbe Application Server</title>
		<link rel=stylesheet href="/graphics/style-login.css">
	</head>
	<body>
	<center>
	<br><br><br>
	
	<div class="content">
	<br/>
	<table><tr><td><a href="/"><img src="/graphics/qbe.sas.topright.png" border="0"></a></td>
		<td><span class="textbig">Systemfehler HTTP/<?=$_SERVER['REDIRECT_STATUS']?></span></td>
	</tr>
	<tr>	<td></td>
		<td class="textinfo" style="width: 400px;">
		<?php
			switch($_SERVER['REDIRECT_STATUS'])
			{
				case 403:
				{
					?>
					Sie besitzen nicht die erforderlichen Berechtitungen um diese URI aufzurufen.<br/>
					Bitte wenden Sie sich an den <a href="mailto:<?=$_SERVER['SERVER_ADMIN']?>">Administrator</a> falls Sie glauben, da&szlig; dies ein Fehler ist.<br/>
					<?php
					break;
				}
				default:
				{
					?>
					Der Server konnte Ihre Anforderung nicht erf&uuml;llen.<br/>
					M&ouml;glicherweise stimmt die Adresse nicht.<br/>
					<?php
					break;
				}
			}
		?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="font-size: 60%;">
			<br/>
			Error <?=$_SERVER['REDIRECT_STATUS']?><br/>
			URI <?=$_SERVER['REDIRECT_URL']?><br/>
			
		</td>
	</tr>
	</table>
	<br/>
	</div>
	</center>
	</body></html>
	
