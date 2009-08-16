<?
	require("../../../sas.inc.php");
	sas_start("Custom Report","../../../","/modules/core/report",2);
	sas_showmenu();

	$f_base = isset($_REQUEST['f_base']) ? $_REQUEST['f_base'] : '';
	$f_end = isset($_REQUEST['f_end']) ? $_REQUEST['f_end'] : '';

	$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
	$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : '';

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	$fields = isset($_REQUEST['fields']) ? explode('|',$_REQUEST['fields']) : '';
	
	if ($name != '')
	{
	
	if ($value == '') 
	{ 	?>
		<form action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<input type=hidden name="f_base" value="<?=$f_base?>">
		<input type=hidden name="f_end" value="<?=$f_end?>">
		<input type=hidden name="name" value="<?=$name?>">
		<input type=hidden name="popup" value="<?=$qbe_popup?>">
		<input type=hidden name="action" value="<?=$action?>">
		<? if ($fields != '') 
		{	$fc=0;
			foreach($fields as $field)
			{echo '<input type=text name="fields['.$fc.']" value="'.$field.'">'; $fc++;}
		} ?>
		
		<?=$name?>: <input type=text name="value" value="<?=$value?>"><br/>
		<button type=submit>Suchen</button>
		
		</form>
		<?php
	} else {
		$fieldlist = '';
		if ($fields != '')
		{	$fc=0;
			foreach($fields as $field)
			{$fieldlist.='&fields['.$fc.']='.$field; $fc++;}
		}
		?>
		Einen Moment bitte, Ihre Suche wird vorbereitet...<br/>
		<br/>
		<meta http-equiv="refresh" content="0; url=list?popup=<?=$qbe_popup?>&action=<?=urlencode($action)?>&<?=$fieldlist?>&filter=<?=urlencode($f_base.$value.$f_end)?>">

		<?php
	}

	} else {
		?><b>Beispiele:</b><br />
		<br/>

		<a href="<?=$_SERVER['PHP_SELF']?>?popup=<?=$qbe_popup?>&name=IP-Adresse&f_base=<?=urlencode('iphostnumber=')?>&f_end=&value=">Suche nach IP-Adresse</a><br />
		<a href="<?=$_SERVER['PHP_SELF']?>?popup=<?=$qbe_popup?>&name=Benutzer-ID&f_base=<?=urlencode('uid=')?>&f_end=&value=">Suche nach Benutzer-ID</a><br />
		<a href="<?=$_SERVER['PHP_SELF']?>?popup=<?=$qbe_popup?>&name=Klasse&f_base=<?=urlencode('(&(objectClass=InetOrgPerson) (ou=')?>&f_end=))&value=">Klasse auflisten</a><br />
		<?php
	}

	sas_end();
