/*
 * Copyright 2003 Christian Hofstaedtler
 */

function popupform(url)
{
        var win = window.open(url,'QbeSAS','width=550,height=300,scrollbars=yes,menubar=no,status=no,toolbar=no,dependent=yes,resizable=yes');
        win.focus();
}
function lookupform(url,field)
{
	var elem = document.getElementById(field);
	var win = window.open(url+'&prefill='+elem.value,'QbeSAS','width=550,height=300,scrollbars=yes,menubar=no,status=no,toolbar=no,dependent=yes,resizable=yes');
	win.focus();
}
/*
 * eof 
 */
