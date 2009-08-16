<?php
	$server = $_SERVER['HTTP_HOST'];
	$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';

	require '../../sas.inc.php';

	if ($qbe_ssl)
		header("Location: https://".$server.$url);
		else
		header("Location: http://".$server.$url);


