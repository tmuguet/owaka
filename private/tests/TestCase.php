<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "EmptyDataSet.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "MySQL55Truncate.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "MySQL55Insert.php";

class TestCase extends Kohana_Unittest_Database_TestCase
{

    protected $useDatabase = TRUE;
    protected $xmlDataSet  = NULL;
    protected $now         = NULL;
    protected $yesterday   = NULL;
    protected $tomorrow    = NULL;
    protected $genNumbers  = array();

    public function __construct()
    {
        Kohana_Kohana_Exception::$error_view = 'kohana/errorPlain';
        Kohana_Kohana_Exception::$error_view_content_type = 'text/plain';
    }

    public function getDataSet()
    {
        if ($this->useDatabase && !empty($this->xmlDataSet)) {
            $callingClass = str_replace('_', DIRECTORY_SEPARATOR, get_called_class());
            $callingDir   = substr($callingClass, 0, strrpos($callingClass, DIRECTORY_SEPARATOR));
            return $this->_getDataSet(
                            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR
                            . $callingDir . DIRECTORY_SEPARATOR
                            . '_files' . DIRECTORY_SEPARATOR . $this->xmlDataSet . '.xml'
            );
        } else {
            return new PHPUnit_Extensions_DataSet_EmptyDataSet();
        }
    }

    private function _GenerateRandom($file)
    {
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
            $genNumbers[]            = $number;
            $this->genNumbers[$name] = $number;
        }
    }

    private function _GenerateId($file)
    {
        $matches = array();
        preg_match_all(
                "/##ID_([A-Za-z0-9]+)##/", file_get_contents($file), $matches
        );
        $names   = array_unique($matches[1]);

        $i = 1;
        foreach ($names as $name) {
            $this->genNumbers[$name] = $i;
            $i++;
        }
    }

    private function _GenerateTmpPath($file)
    {
        $matches = array();
        preg_match_all(
                "/##PATH_([A-Za-z0-9]+)##/", file_get_contents($file), $matches
        );
        $names   = array_unique($matches[1]);

        foreach ($names as $name) {
            $this->genNumbers[$name] = tempnam(sys_get_temp_dir(), 'owaka');
        }
        foreach ($names as $name) {
            unlink($this->genNumbers[$name]);
        }
    }

    private $_dataset = NULL;

    protected function _getDataSet($file)
    {
        if ($this->useDatabase) {
            if ($this->_dataset === NULL) {
                $this->now       = Date::toMySql(time());
                $this->yesterday = Date::toMySql(time() - 3600 * 24);
                $this->tomorrow  = Date::toMySql(time() + 3600 * 24);

                $this->genNumbers = array();
                $this->_GenerateRandom($file);
                $this->_GenerateId($file);
                $this->_GenerateTmpPath($file);

                $ds             = $this->createFlatXmlDataSet($file);
                $this->_dataset = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($ds);
                $this->_dataset->addFullReplacement('##NULL##', NULL);
                $this->_dataset->addFullReplacement('##NOW##', $this->now);
                $this->_dataset->addFullReplacement('##YESTERDAY##', $this->yesterday);
                $this->_dataset->addFullReplacement('##TOMORROW##', $this->tomorrow);
                foreach ($this->genNumbers as $key => $value) {
                    $this->_dataset->addFullReplacement('##RAND_' . $key . '##', $value);
                    $this->_dataset->addFullReplacement('##ID_' . $key . '##', $value);
                    $this->_dataset->addFullReplacement('##PATH_' . $key . '##', $value);
                }
            }
        }
        return $this->_dataset;
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
            Database::instance()->query(Database::DELETE, "SET @PHAKE_PREV_foreign_key_checks = @@foreign_key_checks");
            Database::instance()->query(Database::DELETE, "SET foreign_key_checks = 0");
            $result = Database::instance()->list_tables();
            $tables = array();
            foreach ($result as $row) {
                if (strtolower($row) != "changelog") {
                    $tables[] = $row;
                }
            }
            foreach ($tables as $table) {
                try {
                    Database::instance()->query(Database::DELETE, "truncate table `$table`");
                } catch (Database_Exception $e) {
                    // this is not a table, but a view. delete it
                    Database::instance()->query(Database::DELETE, "drop view if exists `$table`");
                }
            }
            Database::instance()->query(Database::DELETE, "SET foreign_key_checks = @PHAKE_PREV_foreign_key_checks");
        }
        $this->_dataset = NULL;
    }
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "TestCase" . DIRECTORY_SEPARATOR . "Processors.php";
