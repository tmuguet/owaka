<?php
defined('SYSPATH') or die('No direct script access.');

class Model_phpunit_globaldata extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_one = array(
        'build' => array(
            'model'       => 'Build',
            'foreign_key' => 'build_id'),
    );
    // @codingStandardsIgnoreEnd
}
