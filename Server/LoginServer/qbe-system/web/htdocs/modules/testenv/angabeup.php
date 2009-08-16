<?
	// Seitenstart fuer Qbe Application Server
	include "../../sas.inc.php";
	
	sas_start("Angabe hochladen","../../","/modules/testenv",1);
	sas_showmenu();

	// so, ch hat mal code fixed...
	error_reporting(15);	// Warnungen und so
	
	//	newstyle:
	qbe_restrict_access("testarea");

	// real code start:


/*	if ( (isset($_REQUEST['OK'])) && ($_REQUEST['OK'] == '1') )
	{
		// so den rest hab ich jetzt amal grossteils vom filexs modul genommen:
		if (is_uploaded_file($_FILES['file']['tmp_name']))
		{
			$datum=date("Ymd");
			$target="/tmp/".$HTTP_POST_VARS["gegenstand"]."/".$HTTP_POST_VARS["lehrer"]."/".$datum;
			
			copy($_FILES['file']['tmp_name'],$target);
			echo "Jo! Datei (".$target.") is da!<br>Dateiinhalt:<pre>";
			
		
				
			print_r(file($target));

			echo "</pre>";
			
			sas_end();
		} else 
			sas_pcode('error','Dateiname ungueltig.');
	}


		Bei einem File Upload muss man AFAIK method=post und enctype="multipart/form-data" setzen.
*/
?>	<form name="testumgebung" method=post enctype="multipart/form-data">
	<br>
		
		
		

Laden Sie hier die Angabe hoch.<br>
		<div class="box" style="width: 100px;">	
		<a href="javascript:popupform('../filexs/put-popup?actionlink=;hideactions=;show=own;subdir=');">Upload ...</a>
				</div>
		
		
		
				
	
	</form>
<?php

	// END CODE
	sas_end();

