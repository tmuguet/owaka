<?php

class Controller_Widget_Log extends Controller_Widget_BaseRaw
{

    static public function getPreferredSize()
    {
        return parent::getPreferredSize();
    }

    static public function getOptimizedSizes()
    {
        return parent::getOptimizedSizes();
    }

    static public function getExpectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => ($dashboard == 'main')
            )
        );
    }

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'doc';
        $this->widgetTitle = 'log';
    }

    public function action_main()
    {
        return $this->action_project();
    }

    public function action_project()
    {
        $build = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();

        if (!file_exists(APPPATH . 'reports/' . $build->id . '/log.html')) {
            $this->content = 'No data';
        } else {
            $this->content = file_get_contents(APPPATH . 'reports/' . $build->id . '/log.html');
        }

        $this->render();
    }
    
    public function action_sample() {
        $this->content = <<<EOT
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin interdum consectetur molestie. Morbi mi turpis, adipiscing lacinia faucibus ut, lobortis sit amet lorem. Vivamus malesuada, turpis eget gravida pretium, diam mauris convallis diam, a euismod mi lectus non justo. Aenean eleifend mattis pellentesque. Nulla consequat dictum luctus. Phasellus ante quam, commodo a sollicitudin a, tristique vitae quam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vivamus lorem elit, bibendum eget suscipit eget, tempus ac sem. Quisque id urna mi. Suspendisse potenti. Aliquam suscipit cursus metus nec commodo.

Sed sit amet ante arcu, non semper massa. Nam consectetur ullamcorper libero, vel tincidunt nisi placerat eget. Donec sodales, diam nec suscipit interdum, quam ligula adipiscing enim, quis rhoncus turpis turpis sit amet velit. Ut euismod lectus et mauris ultricies eu sagittis mi feugiat. Nam laoreet, nunc eget varius bibendum, odio urna tincidunt leo, sit amet accumsan justo odio et ante. Vestibulum vitae massa est, sit amet lacinia dolor. Praesent dignissim massa eu ipsum vestibulum varius. In lorem nunc, lobortis vel ullamcorper nec, pharetra vitae nulla. Ut sapien sapien, viverra vel cursus eget, venenatis non nibh. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam luctus placerat nulla, sed convallis nisi vulputate euismod. Maecenas pellentesque lacus id nisi suscipit molestie. Curabitur turpis elit, blandit dignissim venenatis sit amet, feugiat ut ante. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin euismod iaculis risus, eget tempor odio suscipit eu. Maecenas consectetur odio at dui euismod consequat. 
EOT;
        $this->render();
    }
}