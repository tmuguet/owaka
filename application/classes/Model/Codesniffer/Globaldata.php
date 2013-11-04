<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Codesniffer data model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Codesniffer_Globaldata extends Model_Data
{

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'warnings' => array(
                array('not_empty'),
                array('integer'),
            ),
            'errors'   => array(
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
        if ($this->thresholdMax($parameters, 'errors', 'error')
                || $this->thresholdMax($parameters, 'warnings', 'error') 
                || $this->thresholdMax($parameters, 'errors_regressions', 'error')
                || $this->thresholdMax($parameters, 'warnings_regressions', 'error')
        ) {
            return Owaka::BUILD_ERROR;
        } else if ($this->thresholdMax($parameters, 'errors', 'unstable')
                || $this->thresholdMax($parameters, 'warnings', 'unstable') 
                || $this->thresholdMax($parameters, 'errors_regressions', 'unstable')
                || $this->thresholdMax($parameters, 'warnings_regressions', 'unstable')
        ) {
            return Owaka::BUILD_UNSTABLE;
        } else {
            return Owaka::BUILD_OK;
        }
    }
}
