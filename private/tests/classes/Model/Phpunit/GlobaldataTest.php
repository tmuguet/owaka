<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Phpunit_GlobaldataTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Model_Phpunit_Globaldata::buildStatus
     * @covers Model_Data::thresholdMax
     */
    public function testBuildStatus()
    {
        $target1           = ORM::factory('Phpunit_Globaldata');
        $target1->errors   = 0;
        $target1->failures = 0;

        $target2           = ORM::factory('Phpunit_Globaldata');
        $target2->errors   = 0;
        $target2->failures = 10;

        $target3           = ORM::factory('Phpunit_Globaldata');
        $target3->errors   = 10;
        $target3->failures = 0;

        $target4           = ORM::factory('Phpunit_Globaldata');
        $target4->errors_regressions   = 10;

        $parameters1 = array(
            'threshold_errors_error'      => -1,
            'threshold_failures_error'    => -1,
            'threshold_errors_unstable'   => -1,
            'threshold_failures_unstable' => -1,
            'threshold_errors_regressions_error'      => 1,
            'threshold_failures_regressions_error'    => 1,
            'threshold_errors_regressions_unstable'   => 1,
            'threshold_failures_regressions_unstable' => 1,
        );

        $parameters2 = array(
            'threshold_errors_error'      => 1,
            'threshold_failures_error'    => -1,
            'threshold_errors_unstable'   => -1,
            'threshold_failures_unstable' => -1,
            'threshold_errors_regressions_error'      => -1,
            'threshold_failures_regressions_error'    => -1,
            'threshold_errors_regressions_unstable'   => -1,
            'threshold_failures_regressions_unstable' => -1,
        );

        $parameters3 = array(
            'threshold_errors_error'      => -1,
            'threshold_failures_error'    => 1,
            'threshold_errors_unstable'   => -1,
            'threshold_failures_unstable' => -1,
            'threshold_errors_regressions_error'      => -1,
            'threshold_failures_regressions_error'    => -1,
            'threshold_errors_regressions_unstable'   => -1,
            'threshold_failures_regressions_unstable' => -1,
        );

        $parameters4 = array(
            'threshold_errors_error'      => -1,
            'threshold_failures_error'    => -1,
            'threshold_errors_unstable'   => 1,
            'threshold_failures_unstable' => -1,
            'threshold_errors_regressions_error'      => -1,
            'threshold_failures_regressions_error'    => -1,
            'threshold_errors_regressions_unstable'   => -1,
            'threshold_failures_regressions_unstable' => -1,
        );

        $parameters5 = array(
            'threshold_errors_error'      => -1,
            'threshold_failures_error'    => -1,
            'threshold_errors_unstable'   => -1,
            'threshold_failures_unstable' => 1,
            'threshold_errors_regressions_error'      => -1,
            'threshold_failures_regressions_error'    => -1,
            'threshold_errors_regressions_unstable'   => -1,
            'threshold_failures_regressions_unstable' => -1,
        );

        $parameters6 = array(
            'threshold_errors_error'      => 20,
            'threshold_failures_error'    => 20,
            'threshold_errors_unstable'   => 20,
            'threshold_failures_unstable' => 20,
            'threshold_errors_regressions_error'      => -1,
            'threshold_failures_regressions_error'    => -1,
            'threshold_errors_regressions_unstable'   => -1,
            'threshold_failures_regressions_unstable' => -1,
        );

        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters6));

        $this->assertEquals(Owaka::BUILD_OK, $target2->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target2->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target2->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target2->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_OK, $target2->buildStatus($parameters6));

        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_ERROR, $target3->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target3->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters6));

        $this->assertEquals(Owaka::BUILD_ERROR, $target4->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target4->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target4->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target4->buildStatus($parameters4));
        $this->assertEquals(Owaka::BUILD_OK, $target4->buildStatus($parameters5));
        $this->assertEquals(Owaka::BUILD_OK, $target4->buildStatus($parameters6));
    }
}