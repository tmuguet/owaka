--//

ALTER TABLE `projects` 
ADD `is_ready` BOOLEAN NOT NULL AFTER `name` ,
ADD `scm_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `scm` ,
ADD `scm_branch` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL AFTER `scm_url`;

ALTER TABLE `projects`
ADD INDEX ( `is_ready` ) ,
ADD INDEX ( `is_active` );

UPDATE `projects` SET `is_ready`=1;

--//@UNDO

--//