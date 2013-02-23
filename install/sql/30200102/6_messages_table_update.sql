ALTER TABLE `{$prefix}messages`
	ADD `font_size` TINYINT UNSIGNED NOT NULL ,
	ADD `font_color` CHAR( 6 ) NOT NULL ,
	ADD `font_face` VARCHAR( 255 ) NOT NULL;