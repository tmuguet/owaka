--//

CREATE TABLE IF NOT EXISTS `project_postactions` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `postaction` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `postaction` (`postaction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `project_postactions`
  ADD CONSTRAINT `project_postactions_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `project_postaction_parameters` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `postaction` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `postaction` (`postaction`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `project_postaction_parameters`
  ADD CONSTRAINT `project_postaction_parameters_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--//@UNDO

--//