<?php
defined('SYSPATH') or die('No direct access allowed!');

class OwakaTestGetReportParameters extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Owaka::getReportParameters
     */
    public function testGetReportParameters()
    {
        $expected = array(
            'threshold_errors_error'                  => -1,
            'threshold_errors_unstable'               => 1,
            'threshold_warnings_error'                => -1,
            'threshold_warnings_unstable'             => 1,
            'threshold_errors_regressions_error'      => -1,
            'threshold_warnings_regressions_error'    => -1,
            'threshold_errors_regressions_unstable'   => 1,
            'threshold_warnings_regressions_unstable' => 1,
        );

        $this->assertEquals($expected, Owaka::getReportParameters(42, 'codesniffer'));
    }

    /**
     * @covers Owaka::getReportParameters
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot find processor foo
     */
    public function testGetReportParameters_errorProcessor()
    {
        Owaka::getReportParameters(42, 'foo');
    }
}