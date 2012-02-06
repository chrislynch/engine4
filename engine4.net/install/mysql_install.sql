CREATE TABLE  `engine4`.`e4_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(1024) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Data` text NOT NULL,
  `XML` text NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE FULLTEXT INDEX e4_fulltext
ON e4_data (XML);

CREATE TABLE `engine4`.`e4_stats` (
  `Year` CHAR(4)  NOT NULL,
  `Month` CHAR(2)  NOT NULL,
  `Day` CHAR(2)  NOT NULL,
  `Stat` VARCHAR(255)  NOT NULL,
  `Value` FLOAT  NOT NULL,
  PRIMARY KEY (`Year`, `Month`, `Day`, `Stat`)
)
ENGINE = MyISAM DEFAULT CHARSET=latin1;

