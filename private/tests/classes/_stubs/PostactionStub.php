<?php

class Postaction_PostactionStub extends Postaction
{

    public static $parameters = array(
        'recipients'  => array(
            'title'        => 'Recipient(s)',
            'description'  => 'List of recipients, separated by spaces/commas',
            'defaultvalue' => ''
        ),
        'on_error'    => array(
            'title'        => 'On error',
            'description'  => 'Send on build error',
            'defaultvalue' => 1
        ),
        'on_unstable' => array(
            'title'        => 'On unstable',
            'description'  => 'Send on unstable build',
            'defaultvalue' => 1
        ),
        'on_ok'       => array(
            'title'        => 'On success',
            'description'  => 'Send on build success',
            'defaultvalue' => 1
        ),
    );

    public function process(Model_Build &$build, array $parameters)
    {
        return true;
    }
}