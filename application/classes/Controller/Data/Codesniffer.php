<?php

class Controller_Data_Codesniffer extends Controller_Data_Base
{

    public function action_parse()
    {
        $build  = $this->request->param('id');
        $report = Owaka::getReportsPath($build, 'codesniffer');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('codesniffer_globaldata');
            $global->build_id = $build;
            $global->warnings = 0;
            $global->errors   = 0;

            $xml = simplexml_load_file($report);
            foreach ($xml->children() as $file) {
                foreach ($file->children() as $item) {
                    $error           = ORM::factory('codesniffer_error');
                    $error->build_id = $build;
                    $error->file     = (string) $file['name'];
                    $error->message  = (string) $item['message'];
                    $error->line     = (int) $item['line'];

                    switch ((string) $item['severity']) {
                        case 'warning':
                            $global->warnings++;
                            $error->severity = 'warning';
                            break;

                        case 'error':
                            $global->errors++;
                            $error->severity = 'error';
                            break;
                    }

                    if (!empty($error->severity)) {
                        $error->create();
                    }
                }
            }

            $global->create();
            $this->response->body(true);
        }

        $this->response->body(false);
    }
}
