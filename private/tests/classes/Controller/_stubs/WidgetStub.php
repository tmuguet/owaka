<?php

class Controller_Widget_WidgetStub extends Controller_Widget
{

    static public function getExpectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => ($dashboard == 'main')
            ),
            'build'   => array(
                'type'     => 'build',
                'required' => false,
            ),
            'foo' => array(
                'title'    => 'Param',
                'type'     => 'enum',
                'enum'     => array('grunge', 'splotchy'),
                'default'  => 'grunge',
                'required' => false
            )
        );
    }

    protected function getWidgetIcon()
    {
        return 'icon';
    }

    protected function getWidgetTitle()
    {
        return 'title';
    }

    protected function render()
    {
        
    }
}