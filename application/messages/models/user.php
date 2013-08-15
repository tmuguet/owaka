<?php
return array(
    'username' => array(
        'not_empty'  => 'You must provide a username.',
        'max_length' => 'The username must be less than :param2 characters long.',
        'unique'     => 'This username is already used.',
    ),
    'password' => array(
        'not_empty' => 'You must provide a password.',
        'different' => 'You must provide a password.',
    ),
    'email'    => array(
        'not_empty' => 'You must provide an email address.',
        'email'     => 'You must provide a valid email address.',
        'unique'    => 'This email address is already used.',
    ),
);