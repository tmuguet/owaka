<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Project report model
 * 
 * @package Model
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

    public function search($projectId, $type)
    {
        $res = $this->where('project_id', '=', $projectId)
                ->where('type', '=', $type)
                ->find();
        if (!$res->loaded() || empty($res->value)) {
            return NULL;
        } else {
            return $res->value;
        }
    }
}
