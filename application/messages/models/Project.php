<?php
return array(
    'name'                  => array(
        'not_empty' => 'You must provide a name.',
    ),
    'is_active'             => array(
        'boolean' => 'You must set your project either active or inactive.',
    ),
    'scm'                   => array(
        'not_empty' => 'You must provide a SCM.',
    ),
    'path'                  => array(
        'not_empty' => 'You must provide a path.',
        'is_dir'    => 'You must provide an existing directory.',
    ),
    'phing_path'            => array(
        'not_empty' => 'You must provide a path.',
        'is_dir'    => 'You must provide an existing directory.',
    ),
    'phing_target_validate' => array(
        'not_empty' => 'You must provide at least one target.',
    ),
    'reports_path'          => array(
        'not_empty' => 'You must provide a path.',
    ),
);