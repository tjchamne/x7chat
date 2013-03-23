CREATE TABLE IF NOT EXISTS `{$prefix}online` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` bigint(20) unsigned NOT NULL,
	`room_id` bigint(20) unsigned NOT NULL,
	`join_timestamp` datetime NOT NULL,
	`part_timestamp` datetime DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`,`room_id`)
) ENGINE=InnoDB;