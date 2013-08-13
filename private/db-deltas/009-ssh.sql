--//

ALTER TABLE `projects` ADD `is_remote` BOOLEAN NOT NULL AFTER `scm` ,
ADD `host` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `is_remote` ,
ADD `port` SMALLINT UNSIGNED NULL AFTER `host` ,
ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `port`,
ADD `privatekey_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `username`,
ADD `server_publichostkey` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `privatekey_path`;

--//@UNDO

--//