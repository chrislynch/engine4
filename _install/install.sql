CREATE TABLE `data` (
  `_ID` int(11) NOT NULL,
  `Field` varchar(255) NOT NULL,
  `Data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `id` (
  `_ID` int(11) NOT NULL AUTO_INCREMENT,
  `_type` varchar(50) DEFAULT '',
  `_timestamp` timestamp NULL DEFAULT NULL,
  `_status` int(11) DEFAULT '0',
  `_security` int(11) DEFAULT '0',
  `_parent` int(11) DEFAULT '0',
  PRIMARY KEY (`_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=latin1;