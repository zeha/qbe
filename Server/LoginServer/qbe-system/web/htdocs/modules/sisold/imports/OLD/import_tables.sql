CREATE TABLE `abteilung` (
  `id` char(1) NOT NULL default '0',
  `Name` varchar(40) default '0',
  `KZ` char(2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE `fach` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `Name` varchar(5) default '0',
  `Beschreibung` varchar(200) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=176 ;

CREATE TABLE `kat` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `Bezeichnung` varchar(128) default NULL,
  `KurzBez` varchar(128) default NULL,
  `showit` tinyint(2) unsigned default NULL,
  `private` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=10 ;

CREATE TABLE `klasse` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `Abteilung` tinyint(5) default '0',
  `Name` char(6) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=56 ;

CREATE TABLE `lehrer` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `KZ` char(2) default NULL,
  `Name` varchar(40) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE `news` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `datum` datetime default NULL,
  `text` text,
  `lehrer` tinyint(3) unsigned default '0',
  `Abteilung` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE `stunde` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `start` time default NULL,
  `ende` time default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE `stundenplan` (
  `WTag` tinyint(1) unsigned default '0',
  `Stunde` tinyint(2) unsigned default '0',
  `Klasse` tinyint(1) unsigned default '0',
  `Fach` varchar(4) default '0',
  `Lehrer` varchar(4) default '0'
) TYPE=MyISAM CHARSET=latin1;

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
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE `tage` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `Name` varchar(40) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=latin1;

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
) TYPE=MyISAM CHARSET=latin1 AUTO_INCREMENT=2 ;