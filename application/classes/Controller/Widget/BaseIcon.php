<?php

abstract class Controller_Widget_BaseIcon extends Controller_Widget_Base
{

    protected $status             = NULL;
    protected $statusData         = NULL;
    protected $statusDataLabel    = NULL;
    protected $substatus          = NULL;
    protected $substatusData      = NULL;
    protected $substatusDataLabel = NULL;

    protected function render()
    {
        if (empty($this->widgetStatus) && !empty($this->status) && empty($this->substatus)) {
            $this->widgetStatus = $this->status;
        }
        
        parent::initViews();
        
        $view = View::factory('widgets/BaseIcon')
                ->set('status', $this->status)
                ->set('statusData', $this->statusData)
                ->set('statusDataLabel', $this->statusDataLabel)
                ->set('substatus', $this->substatus)
                ->set('substatusData', $this->substatusData)
                ->set('substatusDataLabel', $this->substatusDataLabel);

        $this->response->body($view);
    }
}