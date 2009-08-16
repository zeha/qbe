<?php
function get_ibutton($Host,$Port)
{
    $fp = @fsockopen($Host, $Port, $errno, $errstr,400);
    if (!$fp) {
        echo "$errstr ($errno)<br>\n";
    } else {
        @fputs($fp, "Serial\r\n");
        while (!feof($fp)) {
            $Ser = @fgets($fp);
            if ($Ser!="NC") {
                return($Ser);
            }else{
                return("IButton not connected");
            }
        }
    @fclose($fp);
    }
}
?>
