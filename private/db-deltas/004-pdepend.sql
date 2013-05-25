--//

CREATE TABLE IF NOT EXISTS `pdepend_globaldatas` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` mediumint(5) unsigned NOT NULL,
  `ahh` float unsigned NOT NULL,
  `andc` float unsigned NOT NULL,
  `calls` int(10) unsigned NOT NULL,
  `ccn` int(10) unsigned NOT NULL,
  `ccn2` int(10) unsigned NOT NULL,
  `cloc` int(10) unsigned NOT NULL,
  `clsa` int(10) unsigned NOT NULL,
  `clsc` int(10) unsigned NOT NULL,
  `eloc` int(10) unsigned NOT NULL,
  `fanout` int(10) unsigned NOT NULL,
  `leafs` int(10) unsigned NOT NULL,
  `lloc` int(10) unsigned NOT NULL,
  `loc` int(10) unsigned NOT NULL,
  `maxdit` int(10) unsigned NOT NULL,
  `ncloc` int(10) unsigned NOT NULL,
  `noc` int(10) unsigned NOT NULL,
  `nof` int(10) unsigned NOT NULL,
  `noi` int(10) unsigned NOT NULL,
  `nom` int(10) unsigned NOT NULL,
  `nop` int(10) unsigned NOT NULL,
  `roots` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `build_id` (`build_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `pdepend_globaldatas`
  ADD CONSTRAINT `pdepend_globaldatas_ibfk_1` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE;

--//@UNDO

--//