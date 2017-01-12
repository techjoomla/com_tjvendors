CREATE TABLE IF NOT EXISTS `#__tjvendors_passbook` (
`payout_id` INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
`vendor_id` INT(11)  NOT NULL ,
`currency` VARCHAR(255)  NOT NULL ,
`total` INT(11)  NOT NULL ,
`credit` INT(11)  NOT NULL ,
`debit` INT(11)  NOT NULL ,
`reference_order_id` INT(11)  NOT NULL ,
`transaction_time` datetime NOT NULL DEFAULT NOW(),
`client` VARCHAR(255)  NOT NULL ,
`transaction_id` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`payout_id`)

CREATE TABLE IF NOT EXISTS `#__tjvendors_fee` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`vendor_id` INT(11)  NOT NULL ,
`currency` VARCHAR(255)  NOT NULL ,
`client` VARCHAR(255)  NOT NULL ,
`percent_commission` FLOAT(10,2)  NOT NULL ,
`flat_commission` FLOAT(10,2)  NOT NULL ,
PRIMARY KEY (`id`)

) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tjvendors_vendors` (
  `vendor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vendor_title` varchar(255) NOT NULL,
  `vendor_description` text NOT NULL,
  `vendor_logo` varchar(255) NOT NULL,
  `vendor_client` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `params` varchar(255) NOT NULL,
  PRIMARY KEY (`vendor_id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Vendor','com_tjvendors.vendor','{"special":{"dbtable":"#__tjvendors_vendors","key":"id","type":"Vendor","prefix":"TjTable"}}', '{"formFile":"administrator\/components\/com_tjvendors\/models\/forms\/vendor.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_tjvendors.vendor')
) LIMIT 1;
