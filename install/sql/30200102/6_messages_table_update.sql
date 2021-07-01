ALTER TABLE `{$prefix}messages`
	ADD `font_size` TINYINT UNSIGNED NOT NULL DEFAULT '14',
	ADD `font_color` CHAR( 6 ) NOT NULL DEFAULT 'BLACK',
	ADD `font_face` VARCHAR( 255 ) NOT NULL DEFAULT '1';
