CREATE TABLE `{$prefix}users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `group_id` bigint(20) unsigned NOT NULL,
  `banned` tinyint(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  `ip` varchar(45) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `about` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;