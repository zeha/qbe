<?php 
 require("../../sas.inc.php");
 sas_start("","../../","/modules/core/",0,0);

 function initsession() {
 $_SESSION['valid'] = 0;
 $_SESSION['user'] = "";
 $_SESSION['pass'] = "";
 }
 if (!isset($_SESSION['valid'])) { initsession(); }
 if (!isset($_SESSION['user'])) { initsession(); }
 if (!isset($_SESSION['pass'])) { initsession(); }
 $redir = (isset($_GET['go']) ? $_GET['go'] : "");
?>
<html>
<head>
	<title>Qbe Login</title>
	<link rel=stylesheet href="../../graphics/style-login.css">
</head>
<body>
<center>
<br><br><br>

<form action="../<?=$qbe_providers['user']?>/process-login" method=post AUTOCOMPLETE="OFF">

<div class="content">
<br>
<table>
<tr><td><a href="/"><img src="../../graphics/qbe.sas.topright.png" border="0"></a></td>
<td><span class="textbig">Anmeldung</span></td></tr>
<tr><td></td>
<td class="textinfo">

	<table cellpadding=0 cellspacing=0>
	<tr>
	<td>Benutzername:</td>
	<td>&nbsp;Passwort:</td>
	</tr><tr>
	<td><input name="user" id="user" style="width: 120px;" /></td>
	<td>&nbsp;<input type="password" name="pass" style="width: 120px;" /></td>
	</tr>
	<tr><td colspan=2 style="font-size: 2px; height: 2px;">&nbsp;</td></tr>
	<tr>
	<td></td>
	<td>&nbsp;<button type=submit style="width: 120px;">OK</button></td>
	</tr>
	</table>

<?php if (($qbe_ssl) && ($sas_sslstate == "off")) { echo '<br/><a style="color: white;" href="https://'.$_SERVER['HTTP_HOST'].'/modules/core/login?go='.$redir.'">SSL sichert Ihr Passwort</a><br/>'; }?>
	<br/>
</td></tr></table>

<input type="hidden" name="go" value="<?=$redir?>" />
</form>

</div>

<script>
<!--//
	// set focus to Username field
	document.getElementById("user").focus();
//-->
</script>

</body>
</html>

<?
sas_end();

