<?
//
// defines.security.php - defines constant values for SAS
//               this is basically a configuration
//               file
//
// SAS 0.3 - 2003-Okt-01, ch

// This file is a * SECURITY RISK * if it is world readable or
// in the document root!


// mySQL server
$sas_mysql_server = "qbe-sql.system.htlwrn.ac.at";
//   a user with just enough privileges to write into the below mentioned database
//$sas_mysql_user = "nobody";
$sas_mysql_user = "xxx";
//   password for that user
$sas_mysql_password = "XXX";
//   sas data storage db name
$sas_mysql_database = "sas";

// LDAP
$sas_ldap_server = "localhost";
//  sas need administrative access... here the user please:
$sas_ldap_adminuser = "cn=Administrator,ou=Administration,o=htlwrn,c=at";
//  and the password:
$sas_ldap_adminpass = "XXX";
//  sas will logon to ldap in some cases with an unpriviledged user:
$sas_ldap_machineuser = "ou=Administration,o=htlwrn,c=at";
//  it's password:
$sas_ldap_machinepass = "XXX";


//
//

