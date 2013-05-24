--//

ALTER TABLE `projects` CHANGE `scm` `scm` ENUM( 'mercurial', 'git') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

--//@UNDO

--//