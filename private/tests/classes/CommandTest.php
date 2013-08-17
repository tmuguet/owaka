<?php
defined('SYSPATH') or die('No direct access allowed!');

class CommandTest extends TestCase
{

    protected $useDatabase     = FALSE;
    protected $privateKeyPaths = array(
        'thomasmuguet.local' => array('user'       => 'tmuguet', 'privatekey' => '/Users/tmuguet/.ssh/identity', 'key'        => 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArxgfZXjnmOonvyjrDNp64yPflACeeN7U35B2ZenURYHfjUAv0muMsgJK/lzG5CeyHtTzeVGxEUO5sqg5gf8kqGlZfXS5jPZomFFj2FihVV59LgL6bTCWpe85MAWDt6Or4M+UJvkZqspsiRMwe4lTJMFlJU/+T/WoGnyt1l9hNded2msKjgSy3b7eUVIosfk/3S6IJC26/qkyJStsk8Dw4TP3cTTrcDTLwXYKlnUY+6HBs7uisWIwsVgd7q0Tui5YvDKs8jkOaEP8jtD/gHL5VJX+IU1YtwpHiNbJa12F5mJ0qWblhxY4xXlYyiAN12drtXQkI6QwSIh/IsRxuuTBww=='),
        'dev'                => array('user'       => 'tmuguet', 'privatekey' => '/home/owaka/.ssh/owaka', 'key'        => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDMRSvI6VmOXHtENA0He6yHGpvBDKJolVuJmuENfOhK4tcpSxzHu31D7TzazyzP0ujFcaHI8Kfp+DaxSfkjTb0tA94CmbIrswT2lkq55S7iuXqjdobbpF+uIN3RNGypKyOs3t2hflMGyuDQk7EI5uPWObufHHx3RpoW7vl3+HC/MWbV8Sm6UfdSzIw9Npe9WU0B6crnhcWav43MBqUFjb8utD+dSjhMUt1LpDJWp2w/qFpRXrCWWbKTenH4pVmXPAk/9MWGxPQXvrfw9fffCNqStUHR8FpQP3IGo68iG8ikeMsbQ3rCBcdqz+9rfRalYTnq0aKKXA/ao6/+91ZNvRhZ')
    );

    /**
     * @covers Command::__construct
     * @expectedException Exception
     * @expectedExceptionMessage Cannot connect to . Error 0. Failed to parse address ""
     */
    public function testConstructorRemoteEmpty()
    {
        $project                  = ORM::factory('Project');
        $project->is_remote       = TRUE;
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

    /**
     * @covers Command::__construct
     */
    public function testConstructorBadpubkey()
    {
        $project                  = ORM::factory('Project');
        $project->is_remote       = TRUE;
        $project->host            = '127.0.0.1';
        $project->port            = 22;
        $project->public_host_key = 'ssh-rsa NotAKey';

        $host = gethostname();
        if (isset($this->privateKeyPaths[$host])) {
            $project->username        = $this->privateKeyPaths[$host]['user'];
            $public_host_key          = $this->privateKeyPaths[$host]['key'];
            $project->privatekey_path = $this->privateKeyPaths[$host]['privatekey'];
        } else {
            $this->markTestSkipped('Could not find acceptable private key');
        }
        try {
            new Command($project);
        } catch (Exception $e) {
            $this->assertEquals(
                    'Server public host key has changed. Expected: ' . $project->public_host_key . '; Actual: ' . $public_host_key,
                    $e->getMessage()
            );
        }
    }

    public function testLocal()
    {
        $project            = ORM::factory('Project');
        $project->is_remote = FALSE;
        $target             = new Command($project);

        // Chdir
        $target->chdir(APPPATH);
        $this->assertEquals(realpath(APPPATH), $target->pwd());
        $target->chtobasedir();
        $this->assertEquals(realpath(DOCROOT), $target->pwd());

        // Execute
        $result = array();
        exec('ls ' . DOCROOT, $result);
        $actual = implode("\n", $result);
        $this->assertEquals($actual, $target->execute('ls'));

        // Is_dir / Is_file
        $files = array(DOCROOT, APPPATH, APPPATH . DIRECTORY_SEPARATOR . 'bootstrap.php', '/nonexisting', '/var/log/apache');
        foreach ($files as $f) {
            $this->assertEquals(is_dir($f), $target->is_dir($f));
            $this->assertEquals(is_file($f), $target->is_file($f));
        }
    }

    public function testRemote()
    {
        $project            = ORM::factory('Project');
        $project->is_remote = TRUE;
        $project->host      = '127.0.0.1';
        $project->port      = 22;
        $host               = gethostname();
        if (isset($this->privateKeyPaths[$host])) {
            $project->username        = $this->privateKeyPaths[$host]['user'];
            $project->public_host_key = $this->privateKeyPaths[$host]['key'];
            $project->privatekey_path = $this->privateKeyPaths[$host]['privatekey'];
        } else {
            $this->markTestSkipped('Could not find acceptable private key');
        }
        $target = new Command($project);

        // Chdir
        $target->chdir(APPPATH);
        $this->assertEquals(realpath(APPPATH), $target->pwd());
        $target->chtobasedir();
        $this->assertEquals(realpath(APPPATH), $target->pwd());

        // Execute
        $result   = array();
        exec('ls ' . APPPATH, $result);
        $result[] = '';
        $actual   = implode("\n", $result);
        $this->assertEquals($actual, $target->execute('ls'));

        // Is_dir / Is_file
        $files = array(DOCROOT, APPPATH, APPPATH . DIRECTORY_SEPARATOR . 'bootstrap.php', '/nonexisting', '/var/log/apache');
        foreach ($files as $f) {
            $this->assertEquals(is_dir($f), $target->is_dir($f));
            $this->assertEquals(is_file($f), $target->is_file($f));
        }
    }
}