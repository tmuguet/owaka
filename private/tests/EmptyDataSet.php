<?php

class PHPUnit_Extensions_DataSet_EmptyDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{

    protected function createIterator($reverse = FALSE)
    {
        $empty = array();
        return new PHPUnit_Extensions_DataBase_DataSet_DefaultTableIterator($empty, $reverse);
    }

    public function getTable($tableName)
    {
        return NULL;
    }
}