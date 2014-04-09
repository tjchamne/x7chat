ALTER TABLE `{$prefix}messages`
    CHANGE `timestamp` `timestamp` DATETIME NOT NULL,
    CHANGE `dest_type` `dest_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `dest_id` `dest_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `source_type` `source_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `source_id` `source_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `message_type` `message_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    CHANGE `font_size` `font_size` TINYINT(3) UNSIGNED NOT NULL DEFAULT '12',
    CHANGE `font_color` `font_color` CHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '000000',
    CHANGE `font_face` `font_face` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Arial';