--//

CREATE TABLE IF NOT EXISTS `project_report_parameters` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `processor` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `processor` (`processor`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `project_report_parameters`
  ADD CONSTRAINT `project_report_parameters_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--//@UNDO

--//