CREATE TABLE `{$prefix}smilies` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`token` VARCHAR( 255 ) NOT NULL ,
	`image` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;