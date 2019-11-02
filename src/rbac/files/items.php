<?php
return [
    'role:unauthorized'      => [
        'type'     => 1,
        'children' => [
            'perm:restore-password',
        ],
    ],
    'role:admin'             => [
        'type'     => 1,
        'children' => [
            'perm:integrate-account',
        ],
    ],
    'perm:restore-password'  => [
        'type' => 2,
    ],
    'perm:integrate-account' => [
        'type' => 2,
    ],
];