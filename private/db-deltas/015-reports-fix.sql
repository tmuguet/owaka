--//

ALTER TABLE `project_reports` CHANGE `type` `type` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE `project_report_parameters` CHANGE `type` `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

--//@UNDO

--//