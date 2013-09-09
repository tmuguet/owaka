<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * PHPUnit error model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Phpunit_Error extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Belongs to" relationships
     * @var array
     */
    protected $_belongs_to = array(
        'build' => array(
            'model'       => 'Build',
            'foreign_key' => 'build_id'
        ),
    );
    // @codingStandardsIgnoreEnd

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'testsuite' => array(
                array('not_empty'),
            ),
            'testcase'  => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }

    /**
     * Returns whether the error is found in another build
     * 
     * @param Model_Build &$otherBuild Build where the error will be searched for
     * 
     * @return bool
     */
    public function hasSimilar(Model_Build &$otherBuild)
    {
        return ORM::factory('Phpunit_Error')
                        ->where('build_id', '=', $otherBuild->id)
                        ->where('testsuite', '=', $this->testsuite)
                        ->where('testcase', '=', $this->testcase)
                        ->count_all() > 0;
    }
}
