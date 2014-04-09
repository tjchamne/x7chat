ALTER TABLE `{$prefix}rooms`
    CHANGE `topic` `topic` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `greeting` `greeting` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';