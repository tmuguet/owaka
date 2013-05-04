<?php

abstract class Controller_Widget_BaseTable extends Controller_Widget_Base
{

    protected $columnsHeaders = array();
    protected $rows           = array();

    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets/BaseTable')
                ->set('columns', $this->columnsHeaders)
                ->set('rows', $this->rows);

        $this->response->body($view);
    }
}