ALTER TABLE `{$prefix}users` ADD `avatar` VARCHAR( 255 ) NOT NULL AFTER `enable_sounds` ,
ADD `location` VARCHAR( 255 ) NOT NULL AFTER `avatar` ,
ADD `status_type` ENUM( 'available', 'busy', 'away', 'invisible' ) NOT NULL AFTER `location` ,
ADD `status_description` VARCHAR( 255 ) NOT NULL AFTER `status_type`;