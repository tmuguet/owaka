-- Fragment begins: 2 --
INSERT INTO changelog
                                (change_number, delta_set, start_dt, applied_by, description) VALUES (2, 'Main', NOW(), 'dbdeploy', '002-git.sql');
--//

ALTER TABLE `projects` CHANGE `scm` `scm` ENUM( 'mercurial', 'git') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

UPDATE changelog
	                         SET complete_dt = NOW()
	                         WHERE change_number = 2
	                         AND delta_set = 'Main';
-- Fragment ends: 2 --
-- Fragment begins: 3 --
INSERT INTO changelog
                                (change_number, delta_set, start_dt, applied_by, description) VALUES (3, 'Main', NOW(), 'dbdeploy', '003-reports.sql');
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

UPDATE changelog
	                         SET complete_dt = NOW()
	                         WHERE change_number = 3
	                         AND delta_set = 'Main';
-- Fragment ends: 3 --
