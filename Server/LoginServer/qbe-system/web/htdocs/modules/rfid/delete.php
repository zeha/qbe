<?php
require("../../sas.inc.php");
sas_start('delete',"../../","/modules/rfid/",1,0);

require("head.php");
	
	sas_varimport("id");
	$id = intval($id);
	if ($id)
	{
	
	$del = "Delete from entlehnunglog where eid ='".$id."'";
	mysql_query($del);
	$delkey = "Delete from deviceentlehnung where eid = '".$id."'";
	mysql_query($delkey);
	}

	header("Location: accounting.php");


