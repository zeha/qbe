<?
	error_reporting(15);
	$db = mysql_connect("qbe-sql.system.htlwrn.ac.at","sastraffic","htlits");
	
	$res = mysql_query("SELECT SUM(traffic) as SUMTR FROM sas.trafficview");
	$row = mysql_fetch_row($res);
	$maxtraffic = $row[0];
	$traffic['total'] = intval($maxtraffic/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "e"');
	$row = mysql_fetch_row($res);
	$traffic['E'] = intval($row[0]/1024/1024);
	
	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "a"');
	$row = mysql_fetch_row($res);
	$traffic['A'] = intval($row[0]/1024/1024); 

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "h"');
	$row = mysql_fetch_row($res);
	$traffic['H'] = intval($row[0]/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "w"');
	$row = mysql_fetch_row($res);
	$traffic['W'] = intval($row[0]/1024/1024);

	$res = mysql_query('SELECT SUM(traffic) as SUMTR FROM sas.trafficview WHERE abt = "Adm"');
	$row = mysql_fetch_row($res);
	$traffic['adm'] = intval($row[0]/1024/1024);

	$traffic['rest'] = $traffic['total'] - $traffic['E'] - $traffic['A'] - $traffic['H'] - $traffic['W'] - $traffic['adm'];

	header("Content-Type: image/png");
        error_reporting(0);
	include_once("../../includes/3dlib.php");


	$font = 2;
	$bar_num = 5;
	
	$i = @ImageCreate(300, 600) or die("Can't create image");
	
/*	if (isset($_SESSION['qstyle']) && ($_SESSION['qstyle'] == 4))
	{
		$col_fg = ImageColorAllocate($i, 0,0,0);
		$col_bg = ImageColorAllocate($i, 255, 255, 255);
		$white = 20;
	}
		else
	{	# 336699
*/		$col_bg = ImageColorAllocate($i,  51, 102, 153);
		$col_fg = ImageColorAllocate($i,  0,    0,   0);
		$white = 10;
/*	}
*/	
	error_reporting(0);
	
	$C[sizeof($C)] = ImageColorAllocate($i, 255, 0, 0);
	$C[sizeof($C)] = ImageColorAllocate($i, 0, 0, 255);
	$C[sizeof($C)] = ImageColorAllocate($i, 255, 200, 0);
	$C[sizeof($C)] = ImageColorAllocate($i, 0, 255, 0);
	$C[sizeof($C)] = ImageColorAllocate($i, 40, 255, 255);
	$C[sizeof($C)] = ImageColorAllocate($i, 192, 0, 192);
	$C["axis"] = $col_fg;
	$C["grid"] = $col_fg;
	$C["border"] = $col_fg;

	$D[0] = $traffic['A'];
	$D[1] = $traffic['E'];
	$D[2] = $traffic['H'];
	$D[3] = $traffic['W'];
	$D[4] = $traffic['adm'];
	$D[5] = $traffic['rest'];
	$Legend[0] = "AUT";
	$Legend[1] = "ET";
	$Legend[2] = "HB";
	$Legend[3] = "WerkM";
	$Legend[4] = "Adm";
	$Legend[5] = "Rest";
	$L = new C3DLib(50, 530);
	$L->chart_font = $font;
	$L->chart_white = $white;
	$L->mChart3d($i, $D, $Legend, 220, 30, 500, $C, intval($maxtraffic/1024/1024), "", "MB / Dieses Monat");

	ImagePNG($i);

