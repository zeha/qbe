<?php

	// we are playing (have to play) evil here
	
	// we have to open the (hopefully) existing session
	// and destroy (and unset all variables in) it
	//
	// else sas would display the menu with the user logged on etc

	session_start();

	$_SESSION['valid'] = 0;
	$_SESSION['user'] = "";
	$_SESSION['pass'] = "";

	$user = "";
	$pass = "";
	$valid = 0;

	session_unset();
	session_destroy();

	require "../../sas.inc.php";
	sas_start("Abmeldung","../../","/modules/core",0);


		sas_pcode('info','Sie haben sich abgemeldet.');

		?>
		<a href="login">Neu anmelden</a><br>
		<?php
	
	// haben fertisch
	sas_end();
	

