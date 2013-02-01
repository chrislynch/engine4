-- DROP TABLE `trn_cart`;
CREATE  TABLE `trn_cart` (
  `session_id` VARCHAR(255) NOT NULL ,
  `ID` VARCHAR(512) NOT NULL ,
  `QTY` INT NOT NULL ,
  `Data` TEXT NULL ,
  PRIMARY KEY (`session_id`, `ID`) );

-- DROP TABLE `trn_cart_services`;
CREATE TABLE `trn_cart_services` (
  `session_id` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  `Type` varchar(25) NOT NULL,
  `Code` varchar(25) NOT NULL,
  `QTY` int(11) NOT NULL,
  `Price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `Title` varchar(500) NOT NULL,
  `Description` varchar(1024) NOT NULL,
  `Data` text,
  PRIMARY KEY (`session_id`,`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* Do not drop this if it exists - it contains customer order data! */
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

/* Do not drop this table if it exists - it contains user registration data */
CREATE TABLE `trn_newsletter_recipients` (
  `PK` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(500) NOT NULL,
  `name` varchar(1024) NOT NULL,
  `status` varchar(45) NOT NULL,
  PRIMARY KEY (`PK`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

/* Do not drop this table if it exists */
CREATE TABLE `trn_special_order_header` (
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
  `InvoiceNumber` varchar(10) DEFAULT NULL,
  `Despatched` int(11) NOT NULL DEFAULT '0',
  `DespatchTracking` varchar(45) DEFAULT NULL,
  `DespatchDate` timestamp NULL DEFAULT NULL,
  `Data` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/* Do not drop this table if it exists */
CREATE TABLE `trn_special_order_lines` (
  `ID` varchar(255) NOT NULL,
  `ItemID` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

