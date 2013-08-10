<?php
return array(
    'type'   => array(
        'not_empty' => 'You must provide a widget type.',
    ),
    'params' => array(
        'not_empty'  => 'You must provide parameters, even if empty ([]).',
        'min_length' => 'You must provide parameters, even if empty ([]).',
    ),
    'width'  => array(
        'not_empty' => 'You must provide a width.',
        'integer'   => 'Width must be a positive integer',
    ),
    'height' => array(
        'not_empty' => 'You must provide a height.',
        'integer'   => 'Height must be a positive integer',
    ),
    'column' => array(
        'not_empty' => 'You must provide a column.',
        'integer'   => 'Column must be a non-negative integer',
    ),
    'row'    => array(
        'not_empty' => 'You must provide a row.',
        'integer'   => 'Row must be a non-negative integer',
    ),
);