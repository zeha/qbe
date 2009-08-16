<?php

$ds = ldap_connect("qbe-auth.htlwrn.ac.at");

	$r = ldap_bind($ds);

	$sr = ldap_search($ds, "o=htlwrn,c=at", "uid=e99023");

	$info = ldap_get_entries($ds, $sr);

var_dump($info);

?>