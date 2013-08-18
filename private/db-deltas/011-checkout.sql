--//

ALTER TABLE `projects` 
ADD `scm_status` ENUM('void','checkedout','ready') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL AFTER `name` ,
ADD `scm_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `scm` ,
ADD `scm_branch` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `scm_url`;

ALTER TABLE `projects`
ADD INDEX ( `scm_status` ) ,
ADD INDEX ( `is_active` );

UPDATE `projects` SET `scm_status`='ready';

--//@UNDO

--//