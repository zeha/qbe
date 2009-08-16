<?php
function auth(){
    global $userdata;
    $userdata = (isset($_SESSION['userdata']) ? $_SESSION['userdata'] : array('valid' => 0));
    if ($userdata['valid']==0){ 
        return 0;
    }else{
        return 1;
    }
}
?>