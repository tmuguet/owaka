<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Code sniffer errors model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Codesniffer_Error extends ORM
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
            'file'     => array(
                array('not_empty'),
            ),
            'severity' => array(
                array('not_empty'),
                array('in_array', array(':value', array('error', 'warning'))),
            ),
            'message'  => array(
                array('not_empty'),
            ),
            'line'     => array(
                array('not_empty'),
                array('integer'),
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
        return ORM::factory('Codesniffer_Error')
                        ->where('build_id', '=', $otherBuild->id)
                        ->where('file', '=', $this->file)
                        ->where('message', '=', $this->message)
                        ->count_all() > 0;
    }
}
