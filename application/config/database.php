<?php
defined('SYSPATH') or die('No direct access allowed.');

return array
    (
    'default' => array
        (
        'type'         => 'mysql',
        'connection'   => array(
            'hostname'   => '##HOST##',
            'database'   => '##DATABASE##',
            'username'   => '##USER##',
            'password'   => '##PASSWORD##',
            'persistent' => FALSE,
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
        'caching'      => FALSE,
        'profiling'    => TRUE,
    )
);