<?php

abstract class Controller_Widget_BaseTable extends Controller_Widget_Base
{

    protected $columnsHeaders = array();
    protected $rows           = array();

    static public function getPreferredSize()
    {
        return array(2, 4);
    }

    static public function getOptimizedSizes()
    {
        return array(
            array(2, 2), array(2, 4), array(2, 6),
            array(4, 2), array(4, 4), array(4, 6)
        );
    }

    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets/BaseTable')
                ->set('columns', $this->columnsHeaders)
                ->set('rows', $this->rows);

        $this->response->body($view);
    }
}