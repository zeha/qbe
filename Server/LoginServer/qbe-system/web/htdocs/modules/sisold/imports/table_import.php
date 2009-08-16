<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
<title>Importieren der Tabellenstruktur</title>
</head>
<body>
<?php

include("db.inc");

$table_abteilung = "
CREATE TABLE `abteilung` (
  `id` char(1) NOT NULL default '0',
  `Name` varchar(40) default '0',
  `KZ` char(2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1";

$abteil_result = mysql_db_query("sis", $table_abteilung) or die (mysql_error());

$table_fach = "
CREATE TABLE `fach` (
  `id` int(11) unsigned NOT NULL,
  `Name` varchar(5) default '0',
  `Beschreibung` varchar(200) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=1";

$fach_result = mysql_db_query("sis", $table_fach) or die (mysql_error());

$table_kat = "
CREATE TABLE `kat` (
  `id` int(3) unsigned NOT NULL,
  `Bezeichnung` varchar(128) default NULL,
  `KurzBez` varchar(128) default NULL,
  `showit` tinyint(2) unsigned default NULL,
  `private` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=1";

$katresult = mysql_db_query("sis", $table_kat) or die (mysql_error());

$table_klasse = "
CREATE TABLE `klasse` (
  `id` tinyint(3) unsigned NOT NULL,
  `Abteilung` tinyint(5) default '0',
  `Name` char(6) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=1";

$klasse_result = mysql_db_query("sis", $table_klasse) or die (mysql_error());

$table_lehrer = "
CREATE TABLE `lehrer` (
  `id` tinyint(3) unsigned NOT NULL,
  `KZ` char(2) default NULL,
  `Name` varchar(40) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1";

$lehrer_result = mysql_db_query("sis", $table_lehrer) or die (mysql_error());

$table_news = "
CREATE TABLE `news` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `datum` datetime default NULL,
  `text` text,
  `lehrer` tinyint(3) unsigned default '0',
  `Abteilung` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM CHARSET=latin1";

$news_result = mysql_db_query("sis", $table_news) or die (mysql_error());

$table_stunde = "
CREATE TABLE `stunde` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `start` time default NULL,
  `ende` time default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1";

$stunde_result = mysql_db_query("sis", $table_stunde) or die (mysql_error());

$table_stundenplan = "
CREATE TABLE `stundenplan` (
  `WTag` tinyint(1) unsigned default '0',
  `Stunde` tinyint(2) unsigned default '0',
  `Klasse` tinyint(1) unsigned default '0',
  `Fach` varchar(4) default '0',
  `Lehrer` varchar(4) default '0'
) TYPE=MyISAM CHARSET=latin1";

$stundenplan_result = mysql_db_query("sis", $table_stundenplan) or die (mysql_error());

$table_supplierung = "
CREATE TABLE `supplierung` (
  `LfdNr` int(11) unsigned NOT NULL auto_increment,
  `Datum` date default NULL,
  `Klasse` tinyint(3) unsigned default '0',
  `Abteilung` tinyint(3) unsigned default '0',
  `Statt_Lehrer` tinyint(3) unsigned default '0',
  `Statt_Fach` varchar(4) default '0',
  `Stunde` int(3) unsigned default '0',
  `Sup_Lehrer` tinyint(3) unsigned default '0',
  `Sup_Fach` varchar(4) default '0',
  `Entfaellt` tinyint(1) unsigned default '0',
  `MitAuf` tinyint(1) unsigned default NULL,
  `Bemerkung` text,
  `check_klasse` date default NULL,
  `check_lehrer` date default NULL,
  PRIMARY KEY  (`LfdNr`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=1";

$supplierung_result = mysql_db_query("sis", $table_supplierung) or die (mysql_error());

$table_tage = "
CREATE TABLE `tage` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `Name` varchar(40) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1";

$tage_result = mysql_db_query("sis", $table_tage) or die (mysql_error());

$table_termin = "
CREATE TABLE `termin` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `date` date default NULL,
  `time` varchar(128) default NULL,
  `text` text,
  `abteilung` int(3) unsigned default '0',
  `typ` int(11) unsigned NOT NULL default '0',
  `raumnr` varchar(128) default NULL,
  `lehrer` int(5) unsigned default NULL,
  `cn` varchar(128) default NULL,
  `Klasse` int(5) unsigned default NULL,
  `check_klasse` date default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=1;
";

$termin_result = mysql_db_query("sis", $table_termin) or die (mysql_error());
?>
<script>
window.close();
</script>
</body>
</html>