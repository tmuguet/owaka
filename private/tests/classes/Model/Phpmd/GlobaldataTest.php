<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Phpmd_GlobaldataTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Model_Phpmd_Globaldata::buildStatus
     * @covers Model_Data::thresholdMax
     */
    public function testBuildStatus()
    {
        $target1         = ORM::factory('Phpmd_Globaldata');
        $target1->errors = 0;

        $target2         = ORM::factory('Phpmd_Globaldata');
        $target2->errors = 10;

        $target3               = ORM::factory('Phpmd_Globaldata');
        $target3->errors_delta = 1;

        $parameters1 = array(
            'threshold_errors_error'          => -1,
            'threshold_errors_unstable'       => -1,
            'threshold_errors_delta_error'    => -1,
            'threshold_errors_delta_unstable' => 1,
        );

        $parameters2 = array(
            'threshold_errors_error'          => 1,
            'threshold_errors_unstable'       => -1,
            'threshold_errors_delta_error'    => -1,
            'threshold_errors_delta_unstable' => -1,
        );

        $parameters3 = array(
            'threshold_errors_error'          => -1,
            'threshold_errors_unstable'       => 1,
            'threshold_errors_delta_error'    => -1,
            'threshold_errors_delta_unstable' => -1,
        );

        $parameters4 = array(
            'threshold_errors_error'          => 1,
            'threshold_errors_unstable'       => 1,
            'threshold_errors_delta_error'    => -1,
            'threshold_errors_delta_unstable' => -1,
        );

        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target1->buildStatus($parameters4));

        $this->assertEquals(Owaka::BUILD_OK, $target2->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target2->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_ERROR, $target2->buildStatus($parameters4));

        $this->assertEquals(Owaka::BUILD_UNSTABLE, $target3->buildStatus($parameters1));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters2));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters3));
        $this->assertEquals(Owaka::BUILD_OK, $target3->buildStatus($parameters4));
    }
}