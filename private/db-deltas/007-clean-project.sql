--//

ALTER TABLE `projects` DROP `has_parent` ,
DROP `phpunit_report` ,
DROP `phpunit_dir_report` ,
DROP `codesniffer_report` ,
DROP `coverage_report` ,
DROP `coverage_dir_report` ,
DROP `pdepend_report` ,
DROP `phpmd_report` ,
DROP `phpdoc_dir_report` ;

--//@UNDO

--//