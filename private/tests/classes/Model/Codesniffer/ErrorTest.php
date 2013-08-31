<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Codesniffer_ErrorTest extends TestCase
{

    protected $xmlDataSet = 'main';

    /**
     * @covers Model_Codesniffer_Error::hasSimilar
     */
    public function testHasSimilar()
    {
        $target          = new Model_Codesniffer_Error();
        $target->file    = 'foo';
        $target->message = 'foo';

        $target2          = new Model_Codesniffer_Error();
        $target2->file    = 'foo';
        $target2->message = 'bar';

        $build = ORM::factory('Build', $this->genNumbers['build1']);

        $this->assertTrue($target->hasSimilar($build));
        $this->assertFalse($target2->hasSimilar($build));
    }
}