--//

CREATE TABLE IF NOT EXISTS `project_reports` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `type` varchar(30) COLLATE utf8_bin NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `project_reports`
  ADD CONSTRAINT `project_reports_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--//@UNDO

--//