<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_ApiTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Controller_Api::respondOk
     */
    public function testRespondOk()
    {
        $target1 = new Controller_ApiMock();
        $this->assertSame($target1, $target1->respondOk());
        $this->assertEquals(200, $target1->response->status());
        $this->assertEquals(array(), json_decode($target1->response->body(), TRUE));

        $data2   = array('foo', 'bar');
        $target2 = new Controller_ApiMock();
        $target2->respondOk($data2);
        $this->assertEquals(200, $target2->response->status());
        $this->assertEquals($data2, json_decode($target2->response->body(), TRUE));

        $data3   = array('foo'  => 'bar', 'test' => 'test');
        $target3 = new Controller_ApiMock();
        $target3->respondOk($data3);
        $this->assertEquals(200, $target3->response->status());
        $this->assertEquals($data3, json_decode($target3->response->body(), TRUE));
    }

    /**
     * @covers Controller_Api::respondError
     */
    public function testRespondError()
    {
        $target1 = new Controller_ApiMock();
        $this->assertSame($target1, $target1->respondError(Response::BADREQUEST));
        $this->assertEquals(400, $target1->response->status());
        $this->assertEquals(array(), json_decode($target1->response->body(), TRUE));

        $data2   = array('foo', 'bar');
        $target2 = new Controller_ApiMock();
        $target2->respondError(Response::GONE, $data2);
        $this->assertEquals(410, $target2->response->status());
        $this->assertEquals($data2, json_decode($target2->response->body(), TRUE));

        $data3   = array('foo'  => 'bar', 'test' => 'test');
        $target3 = new Controller_ApiMock();
        $target3->respondError(Response::UNPROCESSABLE, $data3);
        $this->assertEquals(422, $target3->response->status());
        $this->assertEquals($data3, json_decode($target3->response->body(), TRUE));
    }
}

class Controller_ApiMock extends Controller_Api
{
    
}