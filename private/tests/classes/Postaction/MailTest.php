<?php

class Postaction_MailTest extends TestCase_Postaction
{

    protected $xmlDataSet = 'mail';

    /**
     * @covers Postaction_Mail::process
     */
    public function testProcess()
    {
        $parameters = Postaction_Mail::projectParameters($this->genNumbers['ProjectFoo']);
        $build1     = ORM::factory('Build', $this->genNumbers['build1']);
        $build2     = ORM::factory('Build', $this->genNumbers['build2']);
        $build3     = ORM::factory('Build', $this->genNumbers['build3']);

        PHPMailer::$mailSent = FALSE;
        $this->assertTrue(
                $this->target->process($build1, $parameters) // ok
        );
        $this->assertTrue(PHPMailer::$mailSent);

        PHPMailer::$mailSent = FALSE;
        $this->assertTrue(
                $this->target->process($build2, $parameters) // error
        );
        $this->assertTrue(PHPMailer::$mailSent);

        PHPMailer::$mailSent = FALSE;
        $this->assertFalse(
                $this->target->process($build3, $parameters) // unstable
        );
        $this->assertFalse(PHPMailer::$mailSent);
    }

    /**
     * @covers Postaction_Mail::send
     */
    public function testSend()
    {
        $parameters = Postaction_Mail::projectParameters($this->genNumbers['ProjectFoo']);
        $build1     = ORM::factory('Build', $this->genNumbers['build1']);
        $build2     = ORM::factory('Build', $this->genNumbers['build2']);
        $build3     = ORM::factory('Build', $this->genNumbers['build3']);

        PHPMailer::$mailSent = FALSE;
        $this->assertTrue($this->target->send($build1, $parameters));
        $this->assertTrue(PHPMailer::$mailSent);

        PHPMailer::$mailSent = FALSE;
        $this->assertTrue($this->target->send($build2, $parameters));
        $this->assertTrue(PHPMailer::$mailSent);

        PHPMailer::$mailSent = FALSE;
        $this->assertTrue($this->target->send($build3, $parameters));
        $this->assertTrue(PHPMailer::$mailSent);
    }
}