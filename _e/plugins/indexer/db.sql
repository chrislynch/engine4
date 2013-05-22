DROP TABLE IF EXISTS `idx_index`;
CREATE  TABLE `idx_index` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `Path` VARCHAR(500) NOT NULL ,
  PRIMARY KEY (`ID`) ,
  UNIQUE INDEX `Path_UNIQUE` (`Path` ASC) );

DROP TABLE IF EXISTS `idx_search`;
CREATE TABLE `idx_search` (
  `ID` int(10) unsigned NOT NULL,
  `search_text` longtext CHARACTER SET utf8,
  `search_xml` longtext,
  FULLTEXT KEY `idx_search_search_text` (`search_text`),
  FULLTEXT KEY `idx_search_search_xml` (`search_xml`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `idx_data`;
CREATE TABLE `idx_XML` (
  `ID` int(11) NOT NULL,
  `XPath` varchar(1024) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;