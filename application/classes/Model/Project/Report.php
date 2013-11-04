<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Project report model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Project_Report extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Has many" relationships
     * @var array
     */
    protected $_belongs_to = array(
        'project' => array(
            'model'       => 'Project',
            'foreign_key' => 'project_id'),
    );
    // @codingStandardsIgnoreEnd

    /**
     * Rule definitions for validation
     *
     * @return array
-     */
    public function rules()
    {
        $rules = array(
            'type'  => array(
                array('not_empty'),
            ),
            'value' => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }

    /**
     * Searches for a report.
     * 
     * @param int    $projectId Project ID
     * @param string $type      Type of report
     * 
     * @return string|null Value or NULL if not found
     */
    public function search($projectId, $type)
    {
        $res = $this->where('project_id', '=', $projectId)
                ->where('type', '=', $type)
                ->find();
        if (!$res->loaded() || $res->value == '') {
            return NULL;
        } else {
            return $res->value;
        }
    }
}
