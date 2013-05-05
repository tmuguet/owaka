<?php

abstract class Controller_Widget_BaseRaw extends Controller_Widget_Base
{

    protected $content = NULL;

    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets/BaseText')
                ->set('content', $this->content);

        $this->response->body($view);
    }
}