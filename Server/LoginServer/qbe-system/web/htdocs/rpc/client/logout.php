<?php
	require "../../sas.inc.php";
	require 'lib.php';

	$c = delLoginHost();

	header("Status: 200");
	if ($c)
		echo "OK LOGOFF ".$c." USERS\n";
		else
		echo "FAIL NOT LOGGED IN\n";
