CREATE TABLE IF NOT EXISTS `#__tj_vendors` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`user_id` INT(11)  NOT NULL ,
`email_id` VARCHAR(255)  NOT NULL ,
`client` VARCHAR(255)  NOT NULL ,
`percent_commission` FLOAT(10,2)  NOT NULL ,
`flat_commission` FLOAT(10,2)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Vendor','com_tjvendors.vendor','{"special":{"dbtable":"#__tj_vendors","key":"id","type":"Vendor","prefix":"TjTable"}}', '{"formFile":"administrator\/components\/com_tjvendors\/models\/forms\/vendor.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_tjvendors.vendor')
) LIMIT 1;
