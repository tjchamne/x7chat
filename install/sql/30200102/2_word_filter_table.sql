CREATE TABLE `{$prefix}word_filters` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`word` VARCHAR( 255 ) NOT NULL ,
	`replacement` VARCHAR( 255 ) NOT NULL ,
	`whole_word_only` BOOL NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;