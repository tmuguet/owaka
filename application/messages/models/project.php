<?php
defined('SYSPATH') OR die('No direct script access.');

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
    'scm_url'               => array(
        'not_empty' => 'You must provide a URL for checking out your project.',
    ),
    'scm_branch'            => array(
        'not_empty' => 'You must provide a branch for checking out your project.',
    ),
    'is_remote'             => array(
        'boolean' => 'You must set your project either local or remote.',
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