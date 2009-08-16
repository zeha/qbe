<?php

$userdata = array('user' => $_SESSION['uid'], 'abteilung' => $_SESSION['abteilung'], 'pw' => $_SESSION['pass'], 'valid' => $_SESSION['valid'], 'id'=>session_id(), 'Klasse' => $_SESSION['ou'], 'abteil'=>$_SESSION['abteilung']);

?>