<?php
defined('SYSPATH') or die('No direct access allowed!');

class CommandTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Command::__construct
     * @expectedException Exception
     * @expectedExceptionMessage Cannot connect to . Error 0. Failed to parse address ""
     */
    public function testConstructorRemoteEmpty()
    {
        $project            = ORM::factory('Project');
        $project->is_remote = TRUE;
        $project->privatekey_path = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'key';
        new Command($project);
    }

    /**
     * @covers Command::__construct
     * @expectedException Exception
     * @expectedExceptionMessage Could not read private key
     */
    public function testConstructorRemoteNoKey()
    {
        $project                  = ORM::factory('Project');
        $project->is_remote       = TRUE;
        $project->host            = '127.0.0.1';
        $project->port            = 22;
        $project->username        = get_current_user();
        $project->privatekey_path = '/none';
        new Command($project);
    }

    /**
     * @covers Command::__construct
     * @expectedException Exception
     * @expectedExceptionMessage Could not login to 127.0.0.1
     */
    public function testConstructorNologin()
    {
        $project                  = ORM::factory('Project');
        $project->is_remote       = TRUE;
        $project->host            = '127.0.0.1';
        $project->port            = 22;
        $project->username        = get_current_user();
        $project->privatekey_path = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'key';
        new Command($project);
    }
}