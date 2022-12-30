CREATE TABLE `{$prefix}messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `dest_type` varchar(255) NOT NULL,
  `dest_id` bigint(20) unsigned NOT NULL,
  `source_type` varchar(255) NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `message_type` varchar(255) NOT NULL,
  `message` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
