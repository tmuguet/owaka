--//

INSERT INTO `roles` (`id`, `name`, `description`) VALUES(3, 'internal', 'Internal user');

INSERT INTO `users` (`id`, `email`, `username`, `password`) VALUES (1, 'owaka-builder@thomasmuguet.info', 'owaka', 'NULL');

INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '1'), ('1', '3');

--//@UNDO

--//