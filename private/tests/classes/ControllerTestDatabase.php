<?php
defined('SYSPATH') or die('No direct access allowed!');

require_once dirname(__FILE__) . DIR_SEP . '_ControllerTest' . DIR_SEP . 'none.php';

class ControllerTestDatabase extends TestCase
{

    protected $xmlDataSet = 'testcase';

    /**
     * @covers Controller::beginTransaction
     * @covers Controller::commitTransaction
     */
    public function testCommit()
    {
        $target = new Controller_none();
        $target->beginTransaction();
        DB::delete('ut')->where('id', '=', $this->genNumbers['Foo'])->execute();
        $target->commitTransaction();

        $count = DB::select(DB::expr('COUNT(*) AS c'))
                ->from('ut')
                ->execute()
                ->get('c');
        $this->assertEquals(2, $count);
    }

    /**
     * @covers Controller::beginTransaction
     * @covers Controller::rollbackTransaction
     */
    public function testRollback()
    {
        $target = new Controller_none();
        $target->beginTransaction();
        DB::delete('ut')->where('id', '=', $this->genNumbers['Foo'])->execute();
        $target->rollbackTransaction();

        $count = DB::select(DB::expr('COUNT(*) AS c'))
                ->from('ut')
                ->execute()
                ->get('c');
        $this->assertEquals(3, $count);
    }

    /**
     * @covers Controller::success
     */
    public function testSuccess()
    {
        $target = new Controller_none();
        $target->beginTransaction();
        DB::delete('ut')->where('id', '=', $this->genNumbers['Foo'])->execute();
        $target->success('foo');

        $count = DB::select(DB::expr('COUNT(*) AS c'))
                ->from('ut')
                ->execute()
                ->get('c');
        $this->assertEquals(2, $count);
        $this->assertEquals('foo', $target->response->body());
    }

    /**
     * @covers Controller::error
     */
    public function testError()
    {
        $target = new Controller_none();
        $target->beginTransaction();
        DB::delete('ut')->where('id', '=', $this->genNumbers['Foo'])->execute();
        $target->error('bar');

        $count = DB::select(DB::expr('COUNT(*) AS c'))
                ->from('ut')
                ->execute()
                ->get('c');
        $this->assertEquals(3, $count);
        $this->assertEquals('bar', $target->response->body());
    }
}