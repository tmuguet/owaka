<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Coverage_GlobaldataTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Model_Coverage_Globaldata::buildStatus
     * @covers Model_Data::thresholdMin
     * @covers Model_Data::thresholdDelta
     */
    public function testBuildStatus()
    {
        $target1                    = ORM::factory('Coverage_Globaldata');
        $target1->methodcoverage    = 100;
        $target1->statementcoverage = 100;
        $target1->totalcoverage     = 100;

        $target2                    = ORM::factory('Coverage_Globaldata');
        $target2->methodcoverage    = 10;
        $target2->statementcoverage = 10;
        $target2->totalcoverage     = 10;

        $target3                       = ORM::factory('Coverage_Globaldata');
        $target3->methodcoverage       = 100;
        $target3->statementcoverage    = 100;
        $target3->totalcoverage        = 100;
        $target3->methodcoverage_delta = -1;

        $parameters1 = array(
            'threshold_methodcoverage_error'             => -1,
            'threshold_statementcoverage_error'          => -1,
            'threshold_totalcoverage_error'              => -1,
            'threshold_methodcoverage_unstable'          => -1,
            'threshold_statementcoverage_unstable'       => -1,
            'threshold_totalcoverage_unstable'           => -1,
            'threshold_methodcoverage_delta_error'       => 0.01,
            'threshold_statementcoverage_delta_error'    => 0.01,
            'threshold_totalcoverage_delta_error'        => 0.01,
            'threshold_methodcoverage_delta_unstable'    => 0.01,
            'threshold_statementcoverage_delta_unstable' => 0.01,
            'threshold_totalcoverage_delta_unstable'     => 0.01,
        );

        $parameters2 = array(
            'threshold_methodcoverage_error'             => 100,
            'threshold_statementcoverage_error'          => -1,
            'threshold_totalcoverage_error'              => -1,
            'threshold_methodcoverage_unstable'          => -1,
            'threshold_statementcoverage_unstable'       => -1,
            'threshold_totalcoverage_unstable'           => -1,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $parameters3 = array(
            'threshold_methodcoverage_error'             => -1,
            'threshold_statementcoverage_error'          => 100,
            'threshold_totalcoverage_error'              => -1,
            'threshold_methodcoverage_unstable'          => -1,
            'threshold_statementcoverage_unstable'       => -1,
            'threshold_totalcoverage_unstable'           => -1,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $parameters4 = array(
            'threshold_methodcoverage_error'             => -1,
            'threshold_statementcoverage_error'          => -1,
            'threshold_totalcoverage_error'              => 100,
            'threshold_methodcoverage_unstable'          => -1,
            'threshold_statementcoverage_unstable'       => -1,
            'threshold_totalcoverage_unstable'           => -1,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $parameters5 = array(
            'threshold_methodcoverage_error'             => -1,
            'threshold_statementcoverage_error'          => -1,
            'threshold_totalcoverage_error'              => -1,
            'threshold_methodcoverage_unstable'          => 100,
            'threshold_statementcoverage_unstable'       => -1,
            'threshold_totalcoverage_unstable'           => -1,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $parameters6 = array(
            'threshold_methodcoverage_error'             => -1,
            'threshold_statementcoverage_error'          => -1,
            'threshold_totalcoverage_error'              => -1,
            'threshold_methodcoverage_unstable'          => -1,
            'threshold_statementcoverage_unstable'       => 100,
            'threshold_totalcoverage_unstable'           => -1,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $parameters7 = array(
            'threshold_methodcoverage_error'             => -1,
            'threshold_statementcoverage_error'          => -1,
            'threshold_totalcoverage_error'              => -1,
            'threshold_methodcoverage_unstable'          => -1,
            'threshold_statementcoverage_unstable'       => -1,
            'threshold_totalcoverage_unstable'           => 100,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $parameters8 = array(
            'threshold_methodcoverage_error'             => 50,
            'threshold_statementcoverage_error'          => 50,
            'threshold_totalcoverage_error'              => 50,
            'threshold_methodcoverage_unstable'          => 100,
            'threshold_statementcoverage_unstable'       => 100,
            'threshold_totalcoverage_unstable'           => 100,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );

        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters6));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters7));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters8));

        $this->assertEquals(Owaka::BUILD_OK, $target2->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target2->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target2->buildStatus($parameters6));
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target2->buildStatus($parameters7));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters8));

        $this->assertEquals(Owaka::BUILD_ERROR, $target3->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters6));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters7));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters8));
    }
}