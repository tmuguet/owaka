<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Base class for models
 * 
 * @package Core
 */
abstract class ORM extends Kohana_ORM
{

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
                        ->from(strtolower(str_replace('Model_', '', get_called_class())) . 's')
                        ->where('id', '=', $id)
                        ->execute()
                        ->get('total') > 0);
    }
}
