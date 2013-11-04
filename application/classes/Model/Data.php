<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract data model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Model_Data extends ORM
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
     * Returns whether a max-threshold is triggered or not
     * 
     * @param array  $parameters Parameters
     * @param string $type       Type
     * @param string $trigger    Trigger (error, unstable)
     * 
     * @return bool Triggered if data is greater or equal to threshold
     */
    protected function thresholdMax(array $parameters, $type, $trigger)
    {
        return ($parameters['threshold_' . $type . '_' . $trigger] > 0 && $this->$type >= $parameters['threshold_' . $type . '_' . $trigger]);
    }

    /**
     * Returns whether a min-threshold is triggered or not
     * 
     * @param array  $parameters Parameters
     * @param string $type       Type
     * @param string $trigger    Trigger (error, unstable)
     * 
     * @return bool Triggered if data is strictly lower than threshold
     */
    protected function thresholdMin(array $parameters, $type, $trigger)
    {
        return ($parameters['threshold_' . $type . '_' . $trigger] > 0 && $this->$type < $parameters['threshold_' . $type . '_' . $trigger]);
    }

    /**
     * Returns whether a delta-threshold is triggered or not
     * 
     * @param array  $parameters Parameters
     * @param string $type       Type
     * @param string $trigger    Trigger (error, unstable)
     * 
     * @return bool Triggered if -data is strictly greater than threshold
     */
    protected function thresholdDelta(array $parameters, $type, $trigger)
    {
        return ($parameters['threshold_' . $type . '_' . $trigger] > 0 && -$this->$type > $parameters['threshold_' . $type . '_' . $trigger]);
    }
}
