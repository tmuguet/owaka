CREATE USER 'owakatest'@'localhost' IDENTIFIED BY 'owakatest';

GRANT USAGE ON * . * TO 'owakatest'@'localhost' IDENTIFIED BY 'owakatest' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

CREATE DATABASE `owakatest` ;

GRANT ALL PRIVILEGES ON `owakatest` . * TO 'owakatest'@'localhost';

USE `owakatest`;

CREATE TABLE `changelog` (
  `change_number` bigint(20) NOT NULL,
  `delta_set` varchar(10) NOT NULL,
  `start_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `complete_dt` timestamp NULL DEFAULT NULL,
  `applied_by` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`change_number`,`delta_set`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;