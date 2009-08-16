# MySQL-Front Dump 2.4
#
# Host: localhost   Database: sis
#--------------------------------------------------------
# Server version 4.0.3-beta-nt

USE sis;


#
# Table structure for table 'klasse'
#

DROP TABLE IF EXISTS klasse;
CREATE TABLE `klasse` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `Abteilung` char(1) default '0',
  `Name` char(3) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'klasse'
#
INSERT INTO klasse (id, Abteilung, Name) VALUES("56", "A", "1AA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("57", "D", "1AD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("58", "E", "1AE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("59", "H", "1AH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("60", "A", "1BA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("61", "D", "1BD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("62", "E", "1BE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("63", "H", "1BH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("64", "D", "1CD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("65", "D", "1DK");
INSERT INTO klasse (id, Abteilung, Name) VALUES("66", "E", "1EF");
INSERT INTO klasse (id, Abteilung, Name) VALUES("67", "H", "1HA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("68", "A", "2AA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("69", "D", "2AD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("70", "E", "2AE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("71", "H", "2AH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("72", "A", "2BA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("73", "D", "2BD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("74", "E", "2BE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("75", "H", "2BH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("76", "D", "2CD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("77", "D", "2DK");
INSERT INTO klasse (id, Abteilung, Name) VALUES("78", "E", "2EF");
INSERT INTO klasse (id, Abteilung, Name) VALUES("79", "E", "2IA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("80", "A", "2MA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("81", "A", "3AA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("82", "D", "3AD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("83", "H", "3AH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("84", "E", "3AI");
INSERT INTO klasse (id, Abteilung, Name) VALUES("85", "A", "3BA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("86", "D", "3BD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("87", "E", "3BE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("88", "H", "3BH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("89", "E", "3EF");
INSERT INTO klasse (id, Abteilung, Name) VALUES("90", "H", "3HA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("91", "A", "4AA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("92", "D", "4AD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("93", "H", "4AH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("94", "E", "4AI");
INSERT INTO klasse (id, Abteilung, Name) VALUES("95", "A", "4BA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("96", "D", "4BD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("97", "E", "4BE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("98", "H", "4BH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("99", "E", "4CE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("100", "E", "4EA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("101", "E", "4EF");
INSERT INTO klasse (id, Abteilung, Name) VALUES("102", "A", "4MA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("103", "A", "5AA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("104", "D", "5AD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("105", "E", "5AE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("106", "H", "5AH");
INSERT INTO klasse (id, Abteilung, Name) VALUES("107", "A", "5BA");
INSERT INTO klasse (id, Abteilung, Name) VALUES("108", "D", "5BD");
INSERT INTO klasse (id, Abteilung, Name) VALUES("109", "E", "5BE");
INSERT INTO klasse (id, Abteilung, Name) VALUES("110", "H", "5BH");
