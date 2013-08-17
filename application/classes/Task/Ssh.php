<?php

class Task_Ssh extends Minion_Task
{

    protected function _execute(array $params)
    {
        $project = new Model_Project();
        $project->is_remote = TRUE;
        $project->host = '127.0.0.1';
        $project->port = 22;
        $project->username = 'tmuguet';
        $project->privatekey_path = '/Users/tmuguet/.ssh/identity';
        
        $command = new Command($project);
        
        $command->rcopy('/Users/tmuguet/Scripts', 'hello');
    }
}
