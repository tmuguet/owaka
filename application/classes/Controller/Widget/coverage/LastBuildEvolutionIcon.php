<?php

class Controller_Widget_coverage_LastBuildEvolutionIcon extends Controller_Widget_BaseIcon
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
        $builds = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->with('coverage_globaldata')
                ->limit(2)
                ->find_all();

        if ($builds->count() != 2 || !$builds[0]->coverage_globaldata->loaded() || !$builds[1]->coverage_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $params = $this->getParameters();
            if (isset($params['display'])) {
                $display = $params['display'];
            } else {
                $display = 'methods+statements';
            }

            $total      = $builds[0]->coverage_globaldata->totalcoverage - $builds[1]->coverage_globaldata->totalcoverage;
            $methods    = $builds[0]->coverage_globaldata->methodcoverage - $builds[1]->coverage_globaldata->methodcoverage;
            $statements = $builds[0]->coverage_globaldata->statementcoverage - $builds[1]->coverage_globaldata->statementcoverage;

            switch ($display) {
                case 'total':
                    if ($total == 0) {
                        $this->widgetStatus    = 'ok';
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->widgetStatus    = ($total > 0 ? 'ok' : 'unstable');
                        $this->status          = $this->widgetStatus;
                        $this->statusData      = ($total > 0 ? '+' . $total : $total) . '%';
                        $this->statusDataLabel = '<br>total';
                    }
                    break;

                case 'methods':
                    if ($methods == 0) {
                        $this->widgetStatus    = 'ok';
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->widgetStatus    = ($methods > 0 ? 'ok' : 'unstable');
                        $this->status          = $this->widgetStatus;
                        $this->statusData      = ($methods > 0 ? '+' . $methods : $methods) . '%';
                        $this->statusDataLabel = '<br>methods';
                    }
                    break;

                case 'statements':
                    if ($statements == 0) {
                        $this->widgetStatus    = 'ok';
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->status          = ($statements > 0 ? 'ok' : 'unstable');
                        $this->statusData      = ($statements > 0 ? '+' . $statements : $statements) . '%';
                        $this->statusDataLabel = '<br>statements';
                    }
                    break;

                default:
                    if ($methods <= 0 && $statements <= 0) {
                        $this->widgetStatus = 'ok';
                    } else {
                        $this->widgetStatus = 'unstable';
                    }
                    if ($methods == 0) {
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->status          = ($methods > 0 ? 'ok' : 'unstable');
                        $this->statusData      = ($methods > 0 ? '+' . $methods : $methods) . '%';
                        $this->statusDataLabel = '<br>methods';
                    }

                    if ($statements == 0) {
                        $this->substatus          = 'ok';
                        $this->substatusData      = '-';
                        $this->substatusDataLabel = '<br>no changes';
                    } else {
                        $this->substatus          = ($statements > 0 ? 'ok' : 'unstable');
                        $this->substatusData      = ($statements > 0 ? '+' . $statements : $statements) . '%';
                        $this->substatusDataLabel = '<br>statements';
                    }
                    break;
            }
        }

        $this->render();
    }
}