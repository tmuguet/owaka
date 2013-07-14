<?php

/**
 * Base class for all widgets displaying tables
 * 
 * @package Widgets
 */
abstract class Controller_Widget_BaseTable extends Controller_Widget_Base
{

    /**
     * Name of the columns
     * @var array
     */
    protected $columnsHeaders = array();
    
    /**
     * List of rows
     * @var array
     */
    protected $rows           = array();

    /**
     * Gets the preferred size (width, height)
     * @return int[]
     */
    static public function getPreferredSize()
    {
        return array(2, 4);
    }

    /**
     * Gets the sizes (width, height) which this widget is optimized for
     * @return int[][]
     */
    static public function getOptimizedSizes()
    {
        return array(
            array(2, 2), array(2, 4), array(2, 6),
            array(4, 2), array(4, 4), array(4, 6)
        );
    }

    /**
     * Renders the widget
     */
    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets' . DIRECTORY_SEPARATOR . 'BaseTable')
                ->set('columns', $this->columnsHeaders)
                ->set('rows', $this->rows);

        $this->response->body($view);
    }
}