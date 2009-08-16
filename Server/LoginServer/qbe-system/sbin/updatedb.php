<?
include "/sas/web/htdocs/includes/user-proxyacl.php";

$argc = $_SERVER['argc'];
if ($argc > 2)
{

	$argv = $_SERVER['argv'];
	$ip = $argv[1];
	$status = $argv[2];
	writeacl($ip,$status);
} else {
	echo "Syntax Error";
}

?>
