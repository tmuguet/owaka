--//

ALTER TABLE `users` ADD `challenge` CHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `users` CHANGE `password` `password` CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin;

--//@UNDO

--//