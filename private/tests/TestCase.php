<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "EmptyDataSet.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "MySQL55Truncate.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "MySQL55Insert.php";

class TestCase extends Kohana_Unittest_Database_TestCase
{

    protected $useDatabase = TRUE;
    protected $xmlDataSet  = NULL;
    protected static $_now        = NULL;
    protected static $_yesterday  = NULL;
    protected static $_tomorrow   = NULL;
    protected static $_genNumbers = array();

    public function getDataSet()
    {
        if ($this->useDatabase && !empty($this->xmlDataSet)) {
            return $this->_getDataSet(dirname(__FILE__) . '/_files/' . $this->xmlDataSet . '.xml');
        } else {
            return new PHPUnit_Extensions_DataSet_EmptyDataSet();
        }
    }

    protected function _getDataSet($file)
    {
        if ($this->useDatabase) {
            if (self::$_now === NULL) {
                self::$_now = Date::toMySql(time());
                self::$_yesterday = Date::toMySql(time() - 3600 * 24);
                self::$_tomorrow = Date::toMySql(time() + 3600 * 24);

                $matches = array();
                preg_match_all(
                        "/##RAND_([A-Za-z0-9]+)##/", file_get_contents($file), $matches
                );
                $names   = array_unique($matches[1]);

                $genNumbers = array();
                foreach ($names as $name) {
                    do {
                        $number = rand(1, 255);
                    } while (in_array($number, $genNumbers));
                    $genNumbers[] = $number;
                    self::$_genNumbers[$name] = $number;
                }
            }


            $ds  = $this->createFlatXmlDataSet($file);
            $rds = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($ds);
            $rds->addFullReplacement('##NULL##', NULL);
            $rds->addFullReplacement('##NOW##', self::$_now);
            $rds->addFullReplacement('##YESTERDAY##', self::$_yesterday);
            $rds->addFullReplacement('##TOMORROW##', self::$_tomorrow);
            foreach (self::$_genNumbers as $key => $value) {
                $rds->addFullReplacement('##RAND_' . $key . '##', $value);
            }
            return $rds;
        } else {
            return NULL;
        }
    }

    public function getSetUpOperation()
    {
        $cascadeTruncates = FALSE; //if you want cascading truncates, false otherwise
        //if unsure choose false

        return new PHPUnit_Extensions_Database_Operation_Composite(array(
            new PHPUnit_Extensions_Database_Operation_MySQL55Truncate($cascadeTruncates, $this),
            new PHPUnit_Extensions_Database_Operation_MySQL55Insert()
        ));
    }

    public function tearDown()
    {
        parent::tearDown();
        if ($this->useDatabase) {
            $connection = $this->getConnection()->getConnection();

            $connection->query("SET @PHAKE_PREV_foreign_key_checks = @@foreign_key_checks");
            $connection->query("SET foreign_key_checks = 0");
            $result = $connection->query("show tables");
            $tables = array();
            while ($row    = $result->fetch(PDO::FETCH_NUM)) {
                if (strtolower($row[0]) != "changelog") {
                    $tables[] = $row[0];
                }
            }
            foreach ($tables as $table) {
                try {
                    $connection->query("truncate table `$table`");
                } catch (PDOException $e) {
                    // this is not a table, but a view. delete it
                    $connection->query("drop view if exists `$table`");
                }
            }
            $connection->query("SET foreign_key_checks = @PHAKE_PREV_foreign_key_checks");
            Database::instance()->disconnect();
        }
        self::$_now = NULL;
    }

    public function assertEquivalent(array $expected, array $actual)
    {
        $this->assertEquals(sizeof($expected), sizeof($actual), 'Sizes of the array differ');
        foreach ($expected as $key => $value) {
            if (!isset($actual[$key])) {
                $this->fail(
                        "Arrays are different at index $key : "
                        . var_export($expected, TRUE) . " / "
                        . var_export($actual, TRUE)
                );
            }
            if (is_array($expected[$key])) {
                if (!is_array($actual[$key])) {
                    $this->fail(
                            "Arrays are different at index $key : "
                            . var_export($expected, TRUE) . " / "
                            . var_export($actual, TRUE)
                    );
                }
                $this->assertEquivalent($expected[$key], $actual[$key]);
            } else {
                $this->assertEquals($expected[$key], $actual[$key], "Arrays are different at key $key");
            }
        }
    }
}