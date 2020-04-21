<?php

return [
    'tag' => [
        'ttl' => false,

        'commands' => [
            'flush' => '*/15 * * * *',
            'reindex'=> '*/30 * * * *'
        ],
    ],

    'key_attribute' => 'id',

    'ttl' => 60 * 60 * 24 * 30,

    'models' => [
        'App\User'
    ]
];
