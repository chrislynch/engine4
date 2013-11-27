CREATE TABLE `trn_cart` (
  `session_id` varchar(255) NOT NULL,
  `Type` varchar(25) NOT NULL,
  `Code` varchar(25) NOT NULL,
  `QTY` int(11) NOT NULL,
  `Price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `Title` varchar(500) NOT NULL,
  `Description` varchar(1024) NOT NULL,
  `Data` text,
  PRIMARY KEY (`session_id`,`Type`, `Code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `trn_order_header` (
  `NO` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID` varchar(25) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `CustomerEMail` varchar(500) DEFAULT NULL,
  `BillingFirstnames` varchar(255) DEFAULT NULL,
  `BillingSurname` varchar(255) DEFAULT NULL,
  `BillingAddress1` varchar(255) DEFAULT NULL,
  `BillingAddress2` varchar(255) DEFAULT NULL,
  `BillingCity` varchar(255) DEFAULT NULL,
  `BillingPostCode` varchar(255) DEFAULT NULL,
  `BillingCountry` varchar(255) DEFAULT NULL,
  `DeliveryFirstnames` varchar(255) DEFAULT NULL,
  `DeliverySurname` varchar(255) DEFAULT NULL,
  `DeliveryAddress1` varchar(255) DEFAULT NULL,
  `DeliveryAddress2` varchar(255) DEFAULT NULL,
  `DeliveryCity` varchar(255) DEFAULT NULL,
  `DeliveryPostCode` varchar(255) DEFAULT NULL,
  `DeliveryCountry` varchar(255) DEFAULT NULL,
  `Created` timestamp NULL DEFAULT NULL,
  `Updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Paid` int(11) DEFAULT '0',
  `PaymentTimestamp` timestamp NULL DEFAULT NULL,
  `PaymentReference` varchar(255) DEFAULT NULL,
  `Despatched` int(11) NOT NULL DEFAULT '0',
  `DespatchTracking` varchar(45) DEFAULT NULL,
  `DespatchDate` timestamp NULL DEFAULT NULL,
  `Data` text,
  PRIMARY KEY (`NO`,`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

/* Do not drop this if it exists - it contains customer order data! */
CREATE TABLE `trn_order_lines` (
  `ID` varchar(255) NOT NULL,
  `ItemID` varchar(512) NOT NULL DEFAULT '0',
  `Code` varchar(255) NOT NULL DEFAULT '',
  `Name` varchar(500) DEFAULT NULL,
  `NetUnitPrice` decimal(8,2) DEFAULT NULL,
  `UnitTax` decimal(8,2) DEFAULT NULL,
  `GrossUnitPrice` decimal(8,2) DEFAULT NULL,
  `QTY` int(11) DEFAULT NULL,
  `NetLinePrice` decimal(8,2) DEFAULT NULL,
  `LineTax` decimal(8,2) DEFAULT NULL,
  `GrossLinePrice` decimal(8,2) DEFAULT NULL,
  `Data` text,
  PRIMARY KEY (`ID`,`ItemID`,`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


