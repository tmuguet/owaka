<?php
defined('SYSPATH') or die('No direct access allowed!');

require_once dirname(__FILE__) . DIR_SEP . '_ControllerTest' . DIR_SEP . 'admin.php';
require_once dirname(__FILE__) . DIR_SEP . '_ControllerTest' . DIR_SEP . 'login.php';
require_once dirname(__FILE__) . DIR_SEP . '_ControllerTest' . DIR_SEP . 'none.php';

class ControllerTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Controller::before
     */
    public function testBeforeLogin()
    {
        $target = new Controller_login();
        $target->request->login();
        $target->before();
    }

    /**
     * @covers Controller::before
     * @expectedException HTTP_Exception_302
     */
    public function testBeforeLoginFail()
    {
        $target = new Controller_login();
        $target->before();
    }

    /**
     * @covers Controller::before
     */
    public function testBeforeAdmin()
    {
        $target = new Controller_admin();
        $target->request->login(Owaka::AUTH_ROLE_ADMIN);
        $target->before();
    }

    /**
     * @covers Controller::before
     * @expectedException HTTP_Exception_302
     */
    public function testBeforeAdminFail()
    {
        $target = new Controller_admin();
        $target->request->login(Owaka::AUTH_ROLE_LOGIN);
        $target->before();
    }

    /**
     * @covers Controller::before
     * @covers Controller::__construct
     */
    public function testBeforeNone()
    {
        $target = new Controller_none();
        $target->before();
    }

    /**
     * @covers Controller::before
     */
    public function testBeforeNoneAction()
    {
        $target = new Controller_admin();
        $target->request->action('nonadmin');
        $target->before();
    }
}