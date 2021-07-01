ALTER TABLE `{$prefix}users`
	ADD `enable_sounds` BOOL NOT NULL DEFAULT '1',
	ADD `use_default_timestamp_settings` BOOL NOT NULL DEFAULT '1',
	ADD `enable_timestamps` BOOL NOT NULL DEFAULT '1',
	ADD `ts_24_hour` BOOL NOT NULL DEFAULT '0',
	ADD `ts_show_seconds` BOOL NOT NULL DEFAULT '0',
	ADD `ts_show_ampm` BOOL NOT NULL DEFAULT '0',
	ADD `ts_show_date` BOOL NOT NULL DEFAULT '0',
	ADD `enable_styles` BOOL NOT NULL DEFAULT '1',
	ADD `message_font_size` TINYINT UNSIGNED NOT NULL DEFAULT '14',
	ADD `message_font_color` CHAR( 6 ) NOT NULL DEFAULT 'BLACK',
	ADD `message_font_face` BIGINT UNSIGNED NOT NULL DEFAULT '1';
