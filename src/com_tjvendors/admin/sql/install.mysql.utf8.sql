CREATE TABLE IF NOT EXISTS `#__tjvendors_passbook` (
`id` INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
`vendor_id` INT(11)  NOT NULL ,
`currency` VARCHAR(255)  NOT NULL ,
`total` DOUBLE(15,2)  NOT NULL ,
`credit` DOUBLE(15,2)  NOT NULL ,
`debit` DOUBLE(15,2)  NOT NULL ,
`reference_order_id` VARCHAR(255)  NOT NULL ,
`transaction_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`client` VARCHAR(255)  NOT NULL ,
`transaction_id` VARCHAR(255)  NOT NULL ,
`status` TINYINT(1)  NOT NULL ,
`params` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tjvendors_fee` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`vendor_id` INT(11)  NOT NULL ,
`currency` VARCHAR(255)  NOT NULL ,
`client` VARCHAR(255)  NOT NULL ,
`percent_commission` DOUBLE(15,2)  NOT NULL ,
`flat_commission` DOUBLE(15,2)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vendor_client_xref` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`vendor_id` INT(11)  NOT NULL ,
`client` VARCHAR(255)  NOT NULL ,
`approved` tinyint(1)  NOT NULL DEFAULT '1',
`state` tinyint(1)  NOT NULL DEFAULT '1',
`params` text DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tjvendors_vendors` (
  `vendor_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vendor_title` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `country` int(3) NOT NULL,
  `region` int(5) NOT NULL,
  `city` varchar(50) NOT NULL,
  `other_city` varchar(50) NOT NULL,
  `zip` varchar(50) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `website_address` varchar(250) NOT NULL,
  `vat_number` varchar(50) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `vendor_description` text NOT NULL,
  `vendor_logo` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
