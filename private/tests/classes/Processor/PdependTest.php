<?php

class Processor_PdependTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->build = ORM::factory('Build', $this->genNumbers['build2']);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Processor_Pdepend::process
     */
    public function testProcess()
    {
        $this->CopyReport(
                'summary',
                dirname(__FILE__) . DIR_SEP . '_files' . DIR_SEP . 'pdepend-summary.xml'
        );

        $this->target->process($this->build);
        $this->commit();

        $globaldataExpected = array(
            array(
                'ahh'    => 2.6,
                'andc'   => 0.8392,
                'calls'  => 525,
                'ccn'    => 402,
                'ccn2'   => 444,
                'cloc'   => 1143,
                'clsa'   => 8,
                'clsc'   => 44,
                'eloc'   => 2623,
                'fanout' => 75,
                'leafs'  => 40,
                'lloc'   => 1283,
                'loc'    => 4194,
                'maxdit' => 6,
                'ncloc'  => 3051,
                'noc'    => 52,
                'nof'    => 0,
                'noi'    => 1,
                'nom'    => 168,
                'nop'    => 9,
                'roots'  => 4
            )
        );
        $globaldata         = DB::select(
                                'ahh', 'andc', 'calls', 'ccn', 'ccn2', 'cloc', 'clsa', 'clsc', 'eloc', 'fanout',
                                'leafs', 'lloc', 'loc', 'maxdit', 'ncloc', 'noc', 'nof', 'noi', 'nom', 'nop', 'roots'
                        )
                        ->from('pdepend_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data4'])
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');
    }

    /**
     * @covers Processor_Pdepend::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->build);
        $this->commit();
        $globaldata = DB::select('ahh')
                        ->from('pdepend_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data4'])
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }
}