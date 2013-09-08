<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Codesniffer_GlobaldataTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Model_Codesniffer_Globaldata::buildStatus
     */
    public function testBuildStatus()
    {
        $target1           = ORM::factory('Codesniffer_Globaldata');
        $target1->errors   = 0;
        $target1->warnings = 0;

        $target2           = ORM::factory('Codesniffer_Globaldata');
        $target2->errors   = 0;
        $target2->warnings = 10;

        $target3           = ORM::factory('Codesniffer_Globaldata');
        $target3->errors   = 10;
        $target3->warnings = 0;

        $parameters1 = array(
            'threshold_errors_error'      => -1,
            'threshold_warnings_error'    => -1,
            'threshold_errors_unstable'   => -1,
            'threshold_warnings_unstable' => -1,
        );

        $parameters2 = array(
            'threshold_errors_error'      => 1,
            'threshold_warnings_error'    => -1,
            'threshold_errors_unstable'   => -1,
            'threshold_warnings_unstable' => -1,
        );

        $parameters3 = array(
            'threshold_errors_error'      => -1,
            'threshold_warnings_error'    => 1,
            'threshold_errors_unstable'   => -1,
            'threshold_warnings_unstable' => -1,
        );

        $parameters4 = array(
            'threshold_errors_error'      => -1,
            'threshold_warnings_error'    => -1,
            'threshold_errors_unstable'   => 1,
            'threshold_warnings_unstable' => -1,
        );

        $parameters5 = array(
            'threshold_errors_error'      => -1,
            'threshold_warnings_error'    => -1,
            'threshold_errors_unstable'   => -1,
            'threshold_warnings_unstable' => 1,
        );

        $parameters6 = array(
            'threshold_errors_error'      => 20,
            'threshold_warnings_error'    => 20,
            'threshold_errors_unstable'   => 20,
            'threshold_warnings_unstable' => 20,
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
    }
}