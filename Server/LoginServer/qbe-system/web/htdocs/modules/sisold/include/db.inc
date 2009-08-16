<?php
require("/qbe/web/htdocs/sas.inc.php");

#$db_server="xtc.system.htlwrn.ac.at";
$db_user="root";
$db_passwd="73v3nstrik3s";

$db_server = $sas_mysql_server;
#$db_user=$sas_mysql_user;
#$db_passwd=$sas_mysql_password;

$db_name="sis";

$db = MYSQL_CONNECT($db_server,$db_user,$db_passwd) or die ("Konnte keine Verbindung zur Datenbank herstellen");
