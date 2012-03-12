CREATE TABLE `e4_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(1024) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `URL` varchar(2000) DEFAULT NULL,
  `Folder` varchar(255) NOT NULL DEFAULT '',
  `Data` text NOT NULL,
  `XML` text NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_content` int(11) NOT NULL DEFAULT '1',
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`ID`),
  FULLTEXT KEY `e4_fulltext` (`XML`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `e4_stats` (
  `Year` CHAR(4)  NOT NULL,
  `Month` CHAR(2)  NOT NULL,
  `Day` CHAR(2)  NOT NULL,
  `ID` INT(11) NOT NULL DEFAULT 0,
  `Stat` VARCHAR(255)  NOT NULL,
  `Value` FLOAT  NOT NULL,
  PRIMARY KEY (`Year`, `Month`, `Day`, `ID`, `Stat`)
)
ENGINE = MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE  `e4_linkage` (
  `ID` int(11) NOT NULL,
  `LinkType` varchar(50) NOT NULL,
  `LinkID` int(11) NOT NULL,
  KEY `IDX_LINKAGE` (`ID`,`LinkType`,`LinkID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE  `e4_tags` (
  `TagID` int(11) NOT NULL AUTO_INCREMENT,
  `TagType` varchar(50) NOT NULL,
  `Tag` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`TagID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
