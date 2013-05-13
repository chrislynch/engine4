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

CREATE  TABLE `trn_order_header` (
  `ID` VARCHAR(25) NOT NULL ,
  `Status` VARCHAR(20) NOT NULL ,
  `CustomerEMail` VARCHAR(500) NULL ,
  `BillingFirstnames` VARCHAR(255) NULL ,
  `BillingSurname` VARCHAR(255) NULL ,
  `BillingAddress1` VARCHAR(255) NULL ,
  `BillingAddress2` VARCHAR(255) NULL ,
  `BillingCity` VARCHAR(255) NULL ,
  `BillingPostCode` VARCHAR(255) NULL ,
  `BillingCountry` VARCHAR(255) NULL ,
  `DeliveryFirstnames` VARCHAR(255) NULL ,
  `DeliverySurname` VARCHAR(255) NULL ,
  `DeliveryAddress1` VARCHAR(255) NULL ,
  `DeliveryAddress2` VARCHAR(255) NULL ,
  `DeliveryCity` VARCHAR(255) NULL ,
  `DeliveryPostCode` VARCHAR(255) NULL ,
  `DeliveryCountry` VARCHAR(255) NULL ,
  `Created` timestamp NULL,
  `Updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Paid` INTEGER NULL DEFAULT 0,
  `PaymentTimestamp` TIMESTAMP NULL,
  `PaymentReference` VARCHAR(255),
  `Despatched` int(11) NOT NULL DEFAULT '0',
  `DespatchTracking` varchar(45) DEFAULT NULL,
  `DespatchDate` timestamp NULL DEFAULT NULL,
  `Data` TEXT NULL ,
  PRIMARY KEY (`ID`) );

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


