<?php 
 /* 
  special version for SAS ...
  mod (c) 2002 Christian Hofstaedtler

 */

include "../../sas.inc.php";
sas_start("Object Details","../../","admin/tools/",2);
sas_showmenu();

#require ('default.php');

$host = $sas_ldap_server; 
$port = 389;
$binddn = $sas_ldap_adminuser;
$bindpw = $sas_ldap_adminpass;
if (isset($_GET['dn'])) { $dn = $_GET['dn']; } else { $dn = ""; }
// if (isset($_GET['filter'])) { $filter = $_GET['filter']; } else { $filter = "objectClass=*"; }
$filter = "objectClass=*";


$ds = ldap_connect ($host, $port);      

if ($ds) { 

    /* Netscape Directory Server will return "Unable to bind to server" if $bindpw is empty */
    if ((strcmp ($binddn, "") == 0) or (strcmp ($bindpw, "") == 0))
        $r = ldap_bind ($ds);
    else
        $r = ldap_bind ($ds, $binddn, $bindpw); 

    $sr = ldap_read ($ds, $dn, $filter);

    $entries_count = ldap_count_entries ($ds, $sr);

    if ($entries_count == 0) {
        /* force $filter to be "objectclass=*" this time */
        $sr = ldap_read ($ds, $dn, "objectclass=*");

        $entries_count = ldap_count_entries ($ds, $sr);

        if ($entries_count == 0) {
            echo "<BR><BR>\n";
            echo "<span class=\"error\">No entry details returned<BR></span>";
	    sas_end();
            return;
        }
    }

    echo "<TABLE class=\"borderon\" border=0>\n";

    $entry = ldap_first_entry ($ds, $sr);

    $attrs = ldap_get_attributes ($ds, $entry);

    echo "<TR>";
    echo "<TD align=right valign=center nowrap><B><I>";
    echo "Distinguished Name" . " = &nbsp;&nbsp;</I></B></TD>";
    echo "<TD align=left valign=top>";
/*    echo "<INPUT type=text ";
//    echo "name=" . "newdn" . " "; 
//    echo "value=\"" . htmlspecialchars ($dn) . "\" size="; 

    if (strlen ($dn) > $default->maxinputlength )
        echo $default->maxinputlength;
    else
        echo strlen ($dn);

//    echo "></TD>";
//    echo "</TR>\n";
*/
	echo $dn . "<br>";
    for ($i = 0; $i < $attrs["count"]; $i++) {

        $attribute = $attrs[$i];
	$attributevalue = ldap_get_values_len ($ds, $entry, $attribute);
	if ($attribute == "userPassword") { continue; }
	if ($attribute == "lmPassword") { continue; }
	if ($attribute == "ntPassword") { continue; }
        for ($j = 0; $j < $attrs[$attribute]["count"]; $j++) {

            echo "<TR>";
            echo "<TD align=right valign=center nowrap><B><I>";
            echo $attrs[$i] . " = </I></B></TD>";

            echo "<TD align=left valign=top>";

            // $value = $attrs[$attribute][$j];
			$value = $attributevalue[$j];

            /* MIME type data output support */
            if (strstr ($attribute, ";jpeg") or strstr ($attribute, ";jpg") or strstr ($attribute, ";jpe") or strstr ($attribute, ";gif") or strstr ($attribute, ";png")) {

			$mm = strlen ($value);
			// echo "attribute = $attribute<BR>";
			// echo "value = $value<BR>";
			// echo "mm = $mm<BR>";
/*
    Because gd 1.6.3 creates PNG images, not GIF images. PNG is a more
    compact format, and full compression is available. Existing code 
    will need modification to call gdImagePng instead of gdImageGif. 

    Unisys holds a patent on the LZW compression algorithm, which is 
    used in fully compressed GIF images. Which ask each website $5000
    bucks if they have gif images. Damn Unisys!!!

    Following codes is another way can let web server output graph directly 
    with generating temporary file.

                $tmpfname = tempnam (".", $default->tmpfile_prefix);

                // get rid of nasty prefix in file path "."
                $tmpfname = substr ($tmpfname, 1);

                $tmpfname = $tmpfname . ".gif";

                $fp = fopen ($tmpfname, "w+");
                fputs ($fp, $value, strlen ($value));
                fclose ($fp);

                $imagesize = GetImageSize ($tmpfname);

                echo "<img src=\"drawgif.php?" . $tmpfname . "\" ";
                echo  $imagesize[3];
                echo " border=0>"; 
*/

                $tmpfname = tempnam (".", $default->tmpfile_prefix);

                /* get rid of nasty prefix in file path "." */
                $tmpfname = substr ($tmpfname, 1);

                if (strstr ($attribute, ";jpeg") or strstr ($attribute, ";jpg") or strstr ($attribute, ";jpe"))
                    $tmpfname = $tmpfname . ".jpg";
                elseif (strstr ($attribute, ";gif"))
                    $tmpfname = $tmpfname . ".gif";
                elseif (strstr ($attribute, ";png"))
                    $tmpfname = $tmpfname . ".png";

                $fulltmpfname = $default->root_html . $default->tmpdir . $tmpfname;

                $fp = fopen ($fulltmpfname, "w+");
                fputs ($fp, $value, strlen ($value));
                fclose ($fp);

                $imagesize = GetImageSize ($fulltmpfname);

                echo "<IMG src=\"" . $default->tmpdir . $tmpfname . "\" ";
                echo  $imagesize[3];
                echo " border=0>"; 

                echo "<BR>";
/*                echo "<INPUT type=text name=default-" . $default->prefix_submit;

                if ($j == 0)
                    echo $attrs[$i] . " "; 
                else
                    echo $attrs[$i] . ";" . $j . " "; 

                echo "type=text";
                echo " value=" . $default->tmpdir . $tmpfname; 
                echo " size=" . strlen ($default->tmpdir . $tmpfname) . ">"; 

                echo "<BR>";
                echo "<INPUT type=hidden name=MAX_FILE_SIZE value=";
                echo $default->maxfsize;
                echo ">";
                echo "<INPUT name=" . $default->prefix_submit;

                if ($j == 0)
                    echo $attrs[$i] . " "; 
                else
                    echo $attrs[$i] . ";" . $j . " "; 

                echo "type=file size=";
                echo $default->maxinputlength - 20;
                echo ">";
*/            }
            else {
                /* NOT MIME type data */
                $isascii = 1;

                for ($k = 0; $k < strlen ($value); $k++) {

                    $chstr = substr ($value, $k, 1);

                    /* if there is non-print character */
                    if ((ord ($chstr) < 20) or (ord ($chstr) > 126)) {
                        $isascii = 0;
                        break;
                    }
                }

                if ($isascii == 0) {

                    echo "<input type=text name=";
                
                    if ($j == 0)
                        echo $default->prefix_submit . $attrs[$i] . " "; 
                    else
                        echo $default->prefix_submit . $attrs[$i] . ";" . $j . " "; 
                
                    echo "value=\"" . base64_encode ($value) . "\" size=";

                    if (strlen (base64_encode ($value)) > $default->maxinputlength )
                        echo $default->maxinputlength;
                    else
                        echo strlen (base64_encode ($value));

                    echo ">";
                }
                else {

                    /* Netscape LDAP aci has double quotes symbols - """ */ 
                    /* ascii 20 - 126, only character """ need to be substitutedin INPUT field so far */
                    /* $value = str_replace ("\"", "&quot;", $value); */
                    /* $value = rawurlencode ($value); */
					$value4size = $value;
                    $value = htmlspecialchars ($value);

/*                    echo "<INPUT name=";

                    if ($j == 0)
                        echo $default->prefix_submit . $attrs[$i] . " "; 
                    else
                        echo $default->prefix_submit . $attrs[$i] . ";" . $j . " "; 

                    if (strstr ($attrs[$i], "userpassword")) {
                        echo "type=password ";
                        echo "value=\"" . $value;
                    }
                    else {
                        echo "type=text ";
                        echo "value=\"" . $value;
                    }

                    echo "\" size=";

                    if (strlen ($value4size) > $default->maxinputlength )
                        echo $default->maxinputlength;
                    else
                        echo strlen ($value4size);
            
                    echo ">";
*/
		    if (strstr ($attrs[$i], "userpassword")) {
                        echo "***<br/>";
                    } else {
			echo $value . "<br/>";
		    }

                }

            }
        
            echo "</TD>";
            echo "</TR>\n";
        }
    }

    ldap_unbind ($ds);
}
else {

    echo "Can not create LDAP connection\n";
}


echo "</TABLE>\n";

sas_end();


?> 
