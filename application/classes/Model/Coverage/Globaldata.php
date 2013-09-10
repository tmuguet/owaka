<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Coverage data model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Coverage_Globaldata extends ORM
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
            'methodcount'       => array(
                array('not_empty'),
                array('integer'),
            ),
            'methodscovered'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'methodcoverage'    => array(
                array('not_empty'),
                array('numeric'),
            ),
            'statementcount'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'statementscovered' => array(
                array('not_empty'),
                array('integer'),
            ),
            'statementcoverage' => array(
                array('not_empty'),
                array('numeric'),
            ),
            'totalcount'        => array(
                array('not_empty'),
                array('integer'),
            ),
            'totalcovered'      => array(
                array('not_empty'),
                array('integer'),
            ),
            'totalcoverage'     => array(
                array('not_empty'),
                array('numeric'),
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
     * 
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function buildStatus(array $parameters)
    {
        if (($parameters['threshold_methodcoverage_error'] > 0 && $this->methodcoverage < $parameters['threshold_methodcoverage_error'])
                || ($parameters['threshold_statementcoverage_error'] > 0 && $this->statementcoverage < $parameters['threshold_statementcoverage_error'])
                || ($parameters['threshold_totalcoverage_error'] > 0 && $this->totalcoverage < $parameters['threshold_totalcoverage_error'])
        ) {
            return Owaka::BUILD_ERROR;
        } else if (($parameters['threshold_methodcoverage_unstable'] > 0 && $this->methodcoverage < $parameters['threshold_methodcoverage_unstable'])
                || ($parameters['threshold_statementcoverage_unstable'] > 0 && $this->statementcoverage < $parameters['threshold_statementcoverage_unstable'])
                || ($parameters['threshold_totalcoverage_unstable'] > 0 && $this->totalcoverage < $parameters['threshold_totalcoverage_unstable'])
        ) {
            return Owaka::BUILD_UNSTABLE;
        } else {
            return Owaka::BUILD_OK;
        }
    }
}
