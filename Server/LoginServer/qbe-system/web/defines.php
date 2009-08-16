<?
//
// defines.php - defines constant values for SAS
//               this is basically a configuration
//               file
//

require('defines.app.php');

$sas_phpext = ".php";

setlocale(LC_ALL,'de_DE');
/*echo 'locale:'.setlocale(LC_ALL,"de_DE.ISO8859-1").'<br>';
$locale = 'de_DE.ISO8859-1';
do {
  $locale_result = setlocale(LC_TIME, $locale);
  echo 'new locale:'.$locale_result.'<br>';
} while ($locale_result != $locale);
*/
include("defines.local.php");
include("defines.security.php");
include("sysstate.php");


$sas_ldap_base = "o=htlwrn,c=at";
$qbe_http_basepath = '/qbe/web/htdocs';

// these can be overridden in defines.local.php
if (isset($_SERVER['SERVER_NAME'])) { $qbe_http_server = $_SERVER['SERVER_NAME']; }
$qbe_ssl = 1;
$qbe_have_rpcclients = 1;

$qbe_app_menu = array();
