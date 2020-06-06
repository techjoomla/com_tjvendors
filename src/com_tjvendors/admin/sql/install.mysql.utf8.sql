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
  `alias` varchar(255) NOT NULL,
  `vendor_description` text NOT NULL,
  `vendor_logo` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` varchar(255) NOT NULL,
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__affiliate_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referer` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__affiliate_conversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `item` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `commision` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__affiliates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `commission` int(11) NOT NULL,
  `user_commission` float(10,2) NOT NULL,
  `affiliates_limit` int(11) NOT NULL,
  `max_per_user` int(11) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `checked_out` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_at` datetime NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Vendor','com_tjvendors.vendor','{"special":{"dbtable":"#__tjvendors_vendors","key":"id","type":"Vendor","prefix":"TjTable"}}', '{"formFile":"administrator\/components\/com_tjvendors\/models\/forms\/vendor.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_tjvendors.vendor')
) LIMIT 1;
