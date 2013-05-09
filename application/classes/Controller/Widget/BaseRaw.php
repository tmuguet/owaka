<?php

abstract class Controller_Widget_BaseRaw extends Controller_Widget_Base
{

    protected $content = NULL;

    static public function getPreferredSize()
    {
        return array(6, 4);
    }

    static public function getOptimizedSizes()
    {
        return array(
            array(4, 2), array(4, 4), array(4, 6),
            array(6, 2), array(6, 4), array(6, 6)
        );
    }

    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets/BaseText')
                ->set('content', $this->content);

        $this->response->body($view);
    }
}