<?
require "../sas.inc.php";
sas_start("LoginCheck","../","/admin",0,0);

if (isset($_GET['go']))	$redir = $_GET['go'];	else $redir = "/";

if ($_SESSION['valid'] != 1)
{	$redir = "index?go=$redir";	}

if (strstr($redir,"?") == "")	$redir .= "?" . md5(time()); else $redir .= "&x=" . md5(time());

header("Location: $redir");

?>
