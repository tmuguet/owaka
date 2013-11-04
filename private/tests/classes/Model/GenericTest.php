<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_GenericTest extends TestCase
{

    protected $xmlDataSet = 'main';

    public function testRules()
    {
        $models = File::getFiles(APPPATH . 'classes' . DIR_SEP . 'Model');
        $pos    = strlen(APPPATH . 'classes' . DIR_SEP);
        foreach ($models as $file) {
            $className = str_replace(DIR_SEP, "_", substr($file, $pos, -4));
            $class = new ReflectionClass($className);
            if ($class->isInstantiable()) {
                $model     = new $className;
                $model->rules();
            }
        }
    }

    /**
     * @covers ORM::duplicate
     */
    public function testDuplicate()
    {
        $target = ORM::factory('Project_Widget', $this->genNumbers['projectBarBackground']);

        $duplicate1    = $target->duplicate()->id;
        $expected1     = ORM::factory('Project_Widget', $this->genNumbers['projectBarBackground']);
        $expected1->id = $duplicate1;
        $actual1       = ORM::factory('Project_Widget', $duplicate1);

        foreach ($actual1->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected1->$column, $actual1->$column, 'Column ' . $column . ' of Project_Widget does not match'
            );
        }


        $duplicate2            = $target->duplicate(array('project_id' => $this->genNumbers['ProjectBat']))->id;
        $expected2             = ORM::factory('Project_Widget', $this->genNumbers['projectBarBackground']);
        $expected2->id         = $duplicate2;
        $expected2->project_id = $this->genNumbers['ProjectBat'];
        $actual2               = ORM::factory('Project_Widget', $duplicate2);

        foreach ($actual2->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected2->$column, $actual2->$column, 'Column ' . $column . ' of Project_Widget does not match'
            );
        }
    }
}