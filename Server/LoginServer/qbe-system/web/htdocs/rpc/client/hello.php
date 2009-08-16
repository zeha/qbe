<?
	$sas_no_redirect = TRUE;	// qbesvc cant handle that correctly
require "../../sas.inc.php";

	qbe_error_handler_off();

	$headers = getallheaders();
	$useragent = $headers['User-Agent'];

	$sysip = sas_web_getclientip();
	$sysmac = sas_web_getclientmac($sysip);

	qbe_log_text("qbe-appmodule-client-hello",LOG_NOTICE,
		'Client Hello from "'.$sysip.'" "'.$sysmac.'" "'.$useragent.'"'
		);


	echo 'OK '.$useragent;
