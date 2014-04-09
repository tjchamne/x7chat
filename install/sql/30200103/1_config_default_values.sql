ALTER TABLE `{$prefix}config`
    CHANGE `version` `version` INT(10) NOT NULL,
    CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `theme` `theme` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default',
    CHANGE `auto_join` `auto_join` BIGINT(20) UNSIGNED NOT NULL,
    CHANGE `use_smtp` `use_smtp` TINYINT(1) NOT NULL,
    CHANGE `smtp_host` `smtp_host` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `smtp_user` `smtp_user` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `smtp_port` `smtp_port` INT(11) NOT NULL,
    CHANGE `smtp_pass` `smtp_pass` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `smtp_mode` `smtp_mode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `from_address` `from_address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `allow_guests` `allow_guests` TINYINT(1) NOT NULL;