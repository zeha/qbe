<?php
	
	sas_showmenu();

	qbe_restrict_access("rfidadm");
	
	
	//Definieren von Variablen
	$group = "rfidadm";

	/* Verbindung aufbauen, auswählen einer Datenbank */
	$link = mysql_connect($sas_mysql_server, $sas_mysql_user, $sas_mysql_password);
	mysql_select_db("rfid");

