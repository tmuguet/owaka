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
class Model_Coverage_Globaldata extends Model_Data
{

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
        if ($this->thresholdMin($parameters, 'methodcoverage', 'error')
                || $this->thresholdMin($parameters, 'statementcoverage', 'error')
                || $this->thresholdMin($parameters, 'totalcoverage', 'error')
                || $this->thresholdDelta($parameters, 'methodcoverage_delta', 'error')
                || $this->thresholdDelta($parameters, 'statementcoverage_delta', 'error')
                || $this->thresholdDelta($parameters, 'totalcoverage_delta', 'error')
        ) {
            return Owaka::BUILD_ERROR;
        } else if ($this->thresholdMin($parameters, 'methodcoverage', 'unstable')
                || $this->thresholdMin($parameters, 'statementcoverage', 'unstable')
                || $this->thresholdMin($parameters, 'totalcoverage', 'unstable')
                || $this->thresholdDelta($parameters, 'methodcoverage_delta', 'unstable')
                || $this->thresholdDelta($parameters, 'statementcoverage_delta', 'unstable')
                || $this->thresholdDelta($parameters, 'totalcoverage_delta', 'unstable')
        ) {
            return Owaka::BUILD_UNSTABLE;
        } else {
            return Owaka::BUILD_OK;
        }
    }
}
