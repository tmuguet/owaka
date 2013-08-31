--//

ALTER TABLE `codesniffer_globaldatas` 
ADD `warnings_regressions` MEDIUMINT( 3 ) UNSIGNED,
ADD `errors_regressions` MEDIUMINT( 3 ) UNSIGNED,
ADD `warnings_fixed` MEDIUMINT( 3 ) UNSIGNED,
ADD `errors_fixed` MEDIUMINT( 3 ) UNSIGNED;

ALTER TABLE `codesniffer_errors` 
ADD `regression` TINYINT( 1 ),
ADD `fixed` TINYINT( 1 );

ALTER TABLE `coverage_globaldatas` 
ADD `methodcoverage_delta` DOUBLE( 10, 2 ),
ADD `statementcoverage_delta` DOUBLE( 10, 2 ),
ADD `totalcoverage_delta` DOUBLE( 10, 2 );

ALTER TABLE `phpmd_globaldatas` 
ADD `errors_delta` MEDIUMINT( 3 );

ALTER TABLE `phpunit_globaldatas` 
ADD `tests_delta` MEDIUMINT( 3 ),
ADD `failures_regressions` MEDIUMINT( 3 ) UNSIGNED, 
ADD `errors_regressions` MEDIUMINT( 3 ) UNSIGNED, 
ADD `failures_fixed` MEDIUMINT( 3 ) UNSIGNED, 
ADD `errors_fixed` MEDIUMINT( 3 ) UNSIGNED,
ADD `time_delta` DOUBLE( 10, 2 );

ALTER TABLE `phpunit_errors` 
ADD `severity` enum('failure','error') COLLATE utf8_bin NOT NULL,
ADD `regression` TINYINT( 1 ),
ADD `fixed` TINYINT( 1 );

--//@UNDO

--//