CREATE TABLE `{$prefix}config` (
  `version` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `theme` varchar(32) NOT NULL,
  `sound_theme` varchar(255) NOT NULL,
  `auto_join` bigint(20) unsigned NOT NULL,
  `use_smtp` tinyint(1) NOT NULL,
  `smtp_host` varchar(255) NOT NULL,
  `smtp_user` varchar(255) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_pass` varchar(255) NOT NULL,
  `smtp_mode` varchar(5) NOT NULL,
  `from_address` varchar(255) NOT NULL,
  `allow_guests` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
