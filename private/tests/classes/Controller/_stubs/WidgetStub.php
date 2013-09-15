<?php

class Controller_Widget_WidgetStub extends Controller_Widget
{

    public static $icon  = 'icon';
    public static $title = 'title';

    static public function expectedParameters($dashboard)
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
            'foo'     => array(
                'title'    => 'Param',
                'type'     => 'enum',
                'enum'     => array('grunge', 'splotchy'),
                'default'  => 'grunge',
                'required' => false
            )
        );
    }

    protected function render()
    {
        
    }
}