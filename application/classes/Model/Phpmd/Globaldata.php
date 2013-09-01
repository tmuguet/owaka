<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * PHPMD data model
 * 
 * @package Model
 */
class Model_Phpmd_Globaldata extends ORM
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
            'errors' => array(
                array('not_empty'),
                array('integer'),
            ),
        );
        return $rules;
    }

    /**
     * Gets the build status according to parameters
     * 
     * @param array $parameters Parameters
     * 
     * @return string
     */
    public function buildStatus(array $parameters)
    {
        if (($parameters['threshold_errors_error'] > 0 && $this->errors >= $parameters['threshold_errors_error'])) {
            return Owaka::BUILD_ERROR;
        } else if (($parameters['threshold_errors_unstable'] > 0 && $this->errors >= $parameters['threshold_errors_unstable'])) {
            return Owaka::BUILD_UNSTABLE;
        } else {
            return Owaka::BUILD_OK;
        }
    }
}
