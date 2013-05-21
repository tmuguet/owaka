--//

CREATE TABLE IF NOT EXISTS `builds` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `revision` varchar(255) COLLATE utf8_bin NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `status` enum('ok','unstable','error','building','queued') COLLATE utf8_bin NOT NULL,
  `started` datetime NOT NULL,
  `eta` datetime DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `build_widgets`
--

CREATE TABLE IF NOT EXISTS `build_widgets` (
  `id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `type` varchar(40) COLLATE utf8_bin NOT NULL,
  `params` text COLLATE utf8_bin,
  `width` smallint(2) unsigned NOT NULL,
  `height` smallint(2) unsigned NOT NULL,
  `column` smallint(2) unsigned NOT NULL,
  `row` smallint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `codesniffer_errors`
--

CREATE TABLE IF NOT EXISTS `codesniffer_errors` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `file` varchar(1024) COLLATE utf8_bin NOT NULL,
  `severity` enum('warning','error') COLLATE utf8_bin NOT NULL,
  `message` varchar(255) COLLATE utf8_bin NOT NULL,
  `line` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `codesniffer_globaldatas`
--

CREATE TABLE IF NOT EXISTS `codesniffer_globaldatas` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `warnings` int(10) unsigned NOT NULL,
  `errors` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `coverage_globaldatas`
--

CREATE TABLE IF NOT EXISTS `coverage_globaldatas` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `methodcount` int(10) unsigned NOT NULL,
  `methodscovered` int(10) unsigned NOT NULL,
  `methodcoverage` double(10,2) unsigned NOT NULL,
  `statementcount` int(10) unsigned NOT NULL,
  `statementscovered` int(10) unsigned NOT NULL,
  `statementcoverage` double(10,2) unsigned NOT NULL,
  `totalcount` int(10) unsigned NOT NULL,
  `totalcovered` int(10) unsigned NOT NULL,
  `totalcoverage` double(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `phpmd_globaldatas`
--

CREATE TABLE IF NOT EXISTS `phpmd_globaldatas` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `errors` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `phpunit_errors`
--

CREATE TABLE IF NOT EXISTS `phpunit_errors` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `testsuite` varchar(255) COLLATE utf8_bin NOT NULL,
  `testcase` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `phpunit_globaldatas`
--

CREATE TABLE IF NOT EXISTS `phpunit_globaldatas` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `tests` int(10) unsigned NOT NULL,
  `failures` int(10) unsigned NOT NULL,
  `errors` int(10) unsigned NOT NULL,
  `time` double(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `scm` enum('mercurial') COLLATE utf8_bin NOT NULL,
  `has_parent` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_bin NOT NULL,
  `phing_path` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `phing_target_validate` varchar(255) COLLATE utf8_bin NOT NULL,
  `phing_target_nightly` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `reports_path` varchar(255) COLLATE utf8_bin NOT NULL,
  `phpunit_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `phpunit_dir_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `codesniffer_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `coverage_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `coverage_dir_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `pdepend_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `phpmd_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `phpdoc_dir_report` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `lastrevision` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `project_widgets`
--

CREATE TABLE IF NOT EXISTS `project_widgets` (
  `id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` mediumint(3) unsigned NOT NULL,
  `type` varchar(40) COLLATE utf8_bin NOT NULL,
  `params` text COLLATE utf8_bin,
  `width` smallint(2) unsigned NOT NULL,
  `height` smallint(2) unsigned NOT NULL,
  `column` smallint(2) unsigned NOT NULL,
  `row` smallint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(40) COLLATE utf8_bin NOT NULL,
  `params` text COLLATE utf8_bin,
  `width` smallint(2) unsigned NOT NULL,
  `height` smallint(2) unsigned NOT NULL,
  `column` smallint(2) unsigned NOT NULL,
  `row` smallint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `builds`
--
ALTER TABLE `builds`
  ADD CONSTRAINT `builds_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `build_widgets`
--
ALTER TABLE `build_widgets`
  ADD CONSTRAINT `build_widgets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `codesniffer_errors`
--
ALTER TABLE `codesniffer_errors`
  ADD CONSTRAINT `codesniffer_errors_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `codesniffer_globaldatas`
--
ALTER TABLE `codesniffer_globaldatas`
  ADD CONSTRAINT `codesniffer_globaldatas_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `coverage_globaldatas`
--
ALTER TABLE `coverage_globaldatas`
  ADD CONSTRAINT `coverage_globaldatas_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `phpmd_globaldatas`
--
ALTER TABLE `phpmd_globaldatas`
  ADD CONSTRAINT `phpmd_globaldatas_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `phpunit_errors`
--
ALTER TABLE `phpunit_errors`
  ADD CONSTRAINT `phpunit_errors_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `phpunit_globaldatas`
--
ALTER TABLE `phpunit_globaldatas`
  ADD CONSTRAINT `phpunit_globaldatas_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `project_widgets`
--
ALTER TABLE `project_widgets`
  ADD CONSTRAINT `project_widgets_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;

--//@UNDO

--//