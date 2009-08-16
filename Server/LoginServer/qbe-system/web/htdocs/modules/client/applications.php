<?php
	require '../../sas.inc.php';
	sas_start("Application Manager","../../","/modules/client/",2);

	sas_varimport('showmore');
	sas_varimport('appname');

	$APPPATH = '/qbe/web/htdocs/applications/';

	$action = 'list';
	if ($showmore != '') { $action = 'detail'; $xmlfile = $showmore; }
	if ($appname != '') { $action = 'distribute'; $xmlfile = $appname; }
	
	if ($action == 'list')
	{

		$dir = opendir('../../applications/');

		echo '<form method=post>'; qbe_web_maketable(true);
		echo '<tr><th colspan=2>Verf&uuml;gbare Applikationen</th></tr>'."\n";

		while ($entry = readdir($dir))
		{
			if (substr($entry,-4) != '.xml')
				continue;
			
			qbe_web_maketr();
			echo ' <td><input type=radio name="appname" value="'.$entry.'" /></td><td><a href="'.$PHP_SELF.'?showmore='.urlencode($entry).'">'.$entry.'</a></td>';
			echo '</tr>'."\n";
		}
		closedir($dir);
		echo '<tr><th colspan=2>Distribute now: <input type=text name=distip value=""> <button type=submit>ok</button></th></tr>';
		echo '</table></form>'."\n";

	} else {
	
		$expr = '/^([a-zA-Z0-9])+\.xml$/';
		if (preg_match($expr,$xmlfile))
		{
	
	function parse_xml($file)
	{
			$xml_parser = xml_parser_create();

			if (!($fp = fopen($file, "r")))
			{
				die("could not open XML input");
			}
			$data = fread($fp, filesize($file));
			fclose($fp);
			xml_parse_into_struct($xml_parser, $data, $vals, $index);
			xml_parser_free($xml_parser);

			// from a comment from php.net
			$params = array();
			$level = array();
			foreach ($vals as $xml_elem) {
			if ($xml_elem['type'] == 'open') {
			if (array_key_exists('attributes',$xml_elem)) {
			list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
			} else {
			$level[$xml_elem['level']] = $xml_elem['tag'];
			}
			}
			if ($xml_elem['type'] == 'complete') {
			$start_level = 1;
			$php_stmt = '$params';
			while($start_level < $xml_elem['level']) {
			$php_stmt .= '[$level['.$start_level.']]';
			$start_level++;
			}
			$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
			eval($php_stmt);
			}
			}
/*
var_dump($vals);
echo "<br>\n";
var_dump($params);
*/
			return $params;
	}
			
			$XMLArray = parse_xml($APPPATH.$xmlfile);
			$XMLArray = $XMLArray['APPLICATIONCONFIG']; //http://www.w3.org/2001/XMLSchema'];
			if ($action == 'detail')
			{
				qbe_web_maketable(true);
				
				qbe_web_maketr();
				echo '<th>Application File:</th><td>http://'.$qbe_http_server.'/applications/'.$xmlfile.' (<a href="http://'.$qbe_http_server.'/applications/'.$xmlfile.'">View</a>)</td></tr>';
				
				qbe_web_maketr();
				echo '<th>Application Name:</th><td>'.$XMLArray['NAME'].'</td></tr>';

				qbe_web_maketr();
				echo '<th>Portable ID:</th><td>'.$XMLArray['PORTABLEID'].'</td></tr>';

				qbe_web_maketr();
				echo '<th>Version Number:</th><td>'.$XMLArray['VERSION'].'</td></tr>';

				qbe_web_maketr();
				echo '<th>Platforms:</th><td>';
				foreach($XMLArray['PLATFORMS'] as $platform)
				{
					echo $platform.', ';
				}
				echo '</td></tr>';

/*				qbe_web_maketr();
				echo '<th>Installation:</th><td><pre>';
				var_dump($XMLArray['INSTALLACTIONS']);
				echo '</pre></td></tr>'; */

				echo '</table>';
			} 
			if ($action == 'distribute')
			{
				sas_varimport('distip');
				sas_varimport('distipbase');
				sas_varimport('distipstart');
				sas_varimport('distipend');
				sas_varimport('refresh');
				if ($distip != '')
				{
					echo 'Distributing application '.$xmlfile.' to '.$distip.'.<br/>';
					$f = fopen('http://'.$distip.':7666/system/distapp?'.urlencode('http://'.$qbe_http_server.'/applications/'.$xmlfile),'r');
					fclose($f);
				}
				else
				{
					echo '"'.$XMLArray['NAME'].'" Version '.$XMLArray['VERSION'];
					
					if ( ($distipbase == '') || ($distipstart == '') || ($distipend == '') )
					{
						echo '<form method=post><input type=hidden name="appname" value="'.$xmlfile.'">';
						echo '<table>';
						echo '<tr><td></td><td style="font-family:monospace;">10.20.254</td><td style="font-family:monospace;">5</td></tr>';
						echo '<tr><td>start:</td><td><input type=text name="distipbase" value="" size=10></td><td><input type=text name="distipstart" value="" size=3></td></tr>';
						echo '<tr><td>end:</td><td><input type=text size=10 disabled style="background-color:gray;"></td><td><input type=text name="distipend" value="" size=3></td></tr>';
						echo '<tr><td colspan=2 align=right><button type=submit>Okay</button></td></tr></table>';
						echo '</form>'."\n";
					} else {
						$url = $_SERVER['PHP_SELF'].'?appname='.urlencode($xmlfile).'&distipbase='.$distipbase.'&distipstart='.$distipstart.'&distipend='.$distipend;
						if ($refresh == '')
						{
							echo '<a href="'.$url.'&refresh='.$distipstart.'">go!</a><br/>';
							$_SESSION['appdist_'.$distipbase] = '';
						} else {
							$thisip = $distipbase . '.' . $refresh;
							echo '<br/>Distributing application to '.$thisip.'...<br/>';
							$f = @fopen('http://'.$thisip.':7666/system/distapp?'.urlencode('http://'.$qbe_http_server.'/applications/'.$xmlfile),'r');
							$x = 'ERROR';
							if ($f)
							{
								fclose($f);
								$x = 'OK';
							}
							$_SESSION['appdist_'.$distipbase] .= $thisip.' '.$x."\n";
							echo '<pre>';
							echo $_SESSION['appdist_'.$distipbase];
							echo '</pre>';
						
							if ($refresh < $distipend)
							{
								$refresh++;
								echo '<meta http-equiv="refresh" content="0; url='.$url.'&refresh='.$refresh.'">Stand by...<br/>';
							}
						}
					}
				}
			}
			
		}
	}

	sas_end();
	
