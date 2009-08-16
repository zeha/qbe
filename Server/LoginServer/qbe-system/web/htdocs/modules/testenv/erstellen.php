<?
	// Seitenstart fuer Qbe Application Server
	include "../../sas.inc.php";
	
	sas_start("Testumgebung erstellen","../../","/modules/testenv",1);
	sas_showmenu();

	// so, ch hat mal code fixed...
	error_reporting(15);	// Warnungen und so
	
	//	newstyle:
	qbe_restrict_access("testarea");

	// real code start:


	
?>	<form name="testumgebung" method=post enctype="multipart/form-data">
	Erstellen Sie hier eine neue Testumgebung.	
		
		
		
			
	
	</form>
<?php

	// END CODE
	sas_end();

