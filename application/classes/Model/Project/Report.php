<?php
defined('SYSPATH') or die('No direct script access.');

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
     * Tests existence of an instance
     * 
     * Returns TRUE iff the object with the specified ID exists
     * 
     * @param int $id Id of the object to test
     * 
     * @return boolean
     */
    public static function exists($id)
    {
        if (empty($id)) {
            return FALSE;
        }
        return (DB::select(array(DB::expr('COUNT(id)'), 'total'))
                        ->from('project_reports')
                        ->where('id', '=', $id)
                        ->execute()
                        ->get('total') > 0);
    }
    
    public function search($projectId, $type) {
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
