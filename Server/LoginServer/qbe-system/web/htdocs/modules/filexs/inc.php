<?php
	function varImport($var,$check = TRUE)
	{	global $$var;
		
		$tmp = isset($_REQUEST[$var]) ? $_REQUEST[$var] : '';
		if ($check)
		{
			if ($tmp != '')
			{
				$tmp = str_replace('..','',$tmp);
				$tmp = str_replace('//','',$tmp);
				if ($tmp[0] == '.') { exit; }
				if ($tmp[0] == '/') { exit; }
				if ($tmp[0] == '"') { exit; }
				if ($tmp[0] == "'") { exit; }
			}
		}
		$$var = $tmp;
	
	}
	function makeBackLink()
	{	global $subdir,$show,$actionlink,$hideactions;
	?>	<a href="./?show=<?=$show?>&subdir=<?=urlencode($subdir)?>&actionlink=<?=urlencode($actionlink)?>&hideactions=<?=$hideactions?>">Zur&uuml;ck</a> 
	<?
	}

	varImport('file');
	varImport('subdir');
	varImport('show');
	varImport('actionlink',FALSE);
	varImport('hideactions',FALSE);
	if ($show == '') {$show = 'own';}
	switch($show)
	{
	case 'own':
		$title = 'Pers&ouml;nliche Ablage';
		$path = '/import/homes/'.$userid; 
		break;
	case 'group':
		$title = 'Gemeinsame Ablagen';
		$path = '/export/groups';
		break;
	case 'common':
		$title = 'Alle';
		$path = '/export/share-alle';
		break;
	default:
		sas_perror('Cannot find target path "'.$show.'"');
		exit;
	}

	$path = $path . '/';
	if($subdir != '')
	{
		$subdir = $subdir .'/';
		$path = $path . $subdir;
	}

function TranslatePerm( $in_Perms ) {
    $sP = '';

    if(($in_Perms & 0xC000) == 0xC000)     // Socket
      $sP = 's';
    elseif(($in_Perms & 0xA000) == 0xA000) // Symbolic Link
      $sP = 'l';
    elseif(($in_Perms & 0x8000) == 0x8000) // Regular
      $sP = '&minus;';
    elseif(($in_Perms & 0x6000) == 0x6000) // Block special
      $sP = 'b';
    elseif(($in_Perms & 0x4000) == 0x4000) // Directory
      $sP = 'd';
    elseif(($in_Perms & 0x2000) == 0x2000) // Character special
      $sP = 'c';
    elseif(($in_Perms & 0x1000) == 0x1000) // FIFO pipe
      $sP = 'p';
    else                         // UNKNOWN
      $sP = 'u';

    // owner
    $sP .= (($in_Perms & 0x0100) ? 'r' : '&minus;') .
           (($in_Perms & 0x0080) ? 'w' : '&minus;') .
           (($in_Perms & 0x0040) ? (($in_Perms & 0x0800) ? 's' : 'x' ) :
                                    (($in_Perms & 0x0800) ? 'S' :
'&minus;'));

    // group
    $sP .= (($in_Perms & 0x0020) ? 'r' : '&minus;') .
           (($in_Perms & 0x0010) ? 'w' : '&minus;') .
           (($in_Perms & 0x0008) ? (($in_Perms & 0x0400) ? 's' : 'x' ) :
                                    (($in_Perms & 0x0400) ? 'S' :
'&minus;'));

    // world
    $sP .= (($in_Perms & 0x0004) ? 'r' : '&minus;') .
            (($in_Perms & 0x0002) ? 'w' : '&minus;') .
            (($in_Perms & 0x0001) ? (($in_Perms & 0x0200) ? 't' : 'x' ) :
                                   (($in_Perms & 0x0200) ? 'T' :
'&minus;'));
        return $sP;
}
