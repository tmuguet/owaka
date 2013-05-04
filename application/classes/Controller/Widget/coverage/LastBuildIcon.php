<?php

class Controller_Widget_coverage_LastBuildIcon extends Controller_Widget_BaseIcon
{

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'target';
        $this->widgetTitle = 'coverage';
    }

    public function action_main()
    {
        return $this->action_project();
    }

    public function action_project()
    {
        $build = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->with('coverage_globaldata')
                ->limit(1)
                ->find();

        if (!$build->coverage_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {

            $params = $this->getParameters();
            if (isset($params['display'])) {
                $display = $params['display'];
            } else {
                $display = 'methods+statements';
            }

            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"  => 'reports/' . $build->id . '/coverage/index.html'
            );

            switch ($display) {
                case 'total':
                    $this->statusData      = floor($build->coverage_globaldata->totalcoverage) . '%';
                    $this->statusDataLabel = '<br/>total';
                    if ($build->coverage_globaldata->totalcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->totalcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    break;
                    
                case 'methods':
                    $this->statusData      = floor($build->coverage_globaldata->methodcoverage) . '%';
                    $this->statusDataLabel = '<br/>methods';
                    if ($build->coverage_globaldata->methodcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->methodcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    break;
                    
                case 'statements':
                    $this->statusData      = floor($build->coverage_globaldata->statementcoverage) . '%';
                    $this->statusDataLabel = '<br/>statements';
                    if ($build->coverage_globaldata->statementcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->statementcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    break;
                    
                default:
                    $this->statusData      = floor($build->coverage_globaldata->methodcoverage) . '%';
                    $this->statusDataLabel = '<br/>methods';
                    if ($build->coverage_globaldata->methodcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->methodcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    
                    $this->substatusData      = floor($build->coverage_globaldata->statementcoverage) . '%';
                    $this->substatusDataLabel = '<br/>statements';
                    if ($build->coverage_globaldata->statementcoverage > 98) {
                        $this->substatus = 'ok';
                    } else if ($build->coverage_globaldata->statementcoverage > 95) {
                        $this->substatus = 'unstable';
                    } else {
                        $this->substatus = 'error';
                    }
                    
                    if ($this->status == 'ok' && $this->substatus == 'ok') {
                        $this->widgetStatus = 'ok';
                    } else {
                        $this->widgetStatus = 'unstable';
                    }
                    break;
            }
        }

        $this->render();
    }
}