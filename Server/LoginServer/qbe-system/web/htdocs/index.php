<?
include "./sas.inc.php";
sas_start("Qbe Authentication Server","/","/",0);

        sas_showmenu(2);
	showclienttext();
	if ($user == "")
	{
		if ($qbe_app_frontpage != '')
			require($qbe_app_frontpage);

	} else {
		showpcconfig();
		showadminpage();
	}

	?>
	<br>
	<br>
	<br>
	<small>choose your color: <a href="<?=$PHP_SELF?>?qstyle=0">the Qwe</a> | <a href="<?=$PHP_SELF?>?qstyle=2">yellow</a> | <a href="<?=$PHP_SELF?>?qstyle=3">bretagne</a> | <a href="<?=$PHP_SELF?>?qstyle=4">snow</a> [not persistent]</small><br>


	<?php
	
	sas_end();

function showpcconfig()
{
	$cl_ip = sas_web_getclientip();
	$cl_mac = sas_web_getclientmac();

	if ($cl_mac != false)
	{
	?>
	Aktueller PC: <?=$cl_ip?> - <?=$cl_mac?><br/>
	<br/>
	<?
	}
}

function showclienttext()
{
	?>
	Aktueller <a href="/modules/client/">Qbe SAS Client</a><br/>
	<br/>
	<?php
}
function showadminpage()
{	global $userid;
	global $qbe_app_adminpage;
	qbe_modules_call("init_app_adminpage");

	function index_php_checkgrouprereq($grouplist,$user)
	{
		$ok = false;
		$groups = split('\|',$grouplist);
		foreach ($groups as $thisgroup)
		{
			if (sas_ldap_isgroupmember($thisgroup,$user)) { $ok=true; }
		}
		return $ok;
	}

function index_php_rendersection($section,$status,$index)
{	global $qbe_ssl,$user;
	$rc = 1;
	
	if (isset($section['_section']['prereq'])) { if ($section['_section']['prereq'] != $status) { return; }}
	if (isset($section['_section']['prereq-group'])) { if (!index_php_checkgrouprereq($section['_section']['prereq-group'],$user)) { return; }}

	?>
	<div id="adminpage_box_<?=$index?>">
	<div class="adminpage_box">
	<table>
	<tr><td valign=top>
	<?php

	if (isset($section['_section']['icon']))  { echo '<img src="'.$section['_section']['icon'].'" border=0 class=icon>'; } else
						  { echo '<img src="/graphics/icons-empty.png" boder=0 width=26 class=icon>'; }

	echo '</td><td valign=top style="padding-top: 5px;">';

#	if (isset($section['_section']['title'])) { echo '<span class="menu_title">'.$section['_section']['title'].'</span>'; }
#	if (isset($section['_section']['right'])) { echo '<span style="right: 2px; position: absolute;">'.$section['_section']['right'].'</span>'; }
	$count = 0;
	$function_have = 0;
	$function_rc = 0;
	foreach ($section as $key => $entry)
	{
		$mode = 'link';
		if (isset($entry['mode'])) {$mode = $entry['mode']; }
		if (isset($entry['prereq'])) { if ($entry['prereq'] != $status) {continue;}}
		if (isset($entry['prereq-group'])) { if (!index_php_checkgrouprereq($entry['prereq-group'],$user)) { continue; }}
		if ($mode == 'link')
		{
			if (!isset($entry['link'])) { continue; }
			
			?><a href="<?=$entry['link']?>"><?=$entry['text']?></a><?php
	
			if ($qbe_ssl and isset($entry['add-ssl'])) { if ($entry['add-ssl'] == TRUE)
			{	?>
				(<a href="/modules/redir/ssl.php?url=<?=urlencode($entry['link'])?>">ssl</a>)
			<?php
			}}
			?><br /><?php
		}
		if ($mode == 'text')
		{
			?><?=$entry['text']?><br /><?php
		}
		if ($mode == 'code')
		{
			$function_rc = $entry['function']();
			$function_have = 1;
		}
		$count++;
	}
	if (($count == 1) && ($function_have))
	{
		$rc = $function_rc;
	}

	?></tr></table>
	</div>
	<div style="height: 3px;"> </div>
	</div>
	<?php
	return $rc;
}

	$status = 'login';
	if ( (!isset($userid)) || ($userid == "")) { $status = 'nologin'; }


	if (isset($qbe_app_adminpage['_top']))
		index_php_rendersection($qbe_app_adminpage['_top'],$status,0);
	if (isset($qbe_app_adminpage['_user']))
		index_php_rendersection($qbe_app_adminpage['_user'],$status,1);
	$index = 2;
	foreach ($qbe_app_adminpage as $key => $section)
	{
		if ($key == '_top') { continue; }
		if ($key == '_user') { continue; }
		if (index_php_rendersection($section,$status,++$index) == 0)
		{
		?>
		<style>#adminpage_box_<?=$index?> { visibility: hidden; height: 0px; }</style>
		<?
		}
	}
}

