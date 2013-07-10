<?php
defined('SYSPATH') OR die('No direct access allowed.');

return array(
    'driver'       => 'ORM',
    'hash_key'     => '##HASHKEY##',
    'lifetime'     => 1209600,
    'session_type' => 'cookie',
    'session_key'  => 'auth_user',
);
