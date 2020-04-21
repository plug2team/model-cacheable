<?php

return [
    'tag' => [
        'permanent' => true
    ],

    'key_attribute' => 'id',

    'ttl' => 60 * 60 * 24 * 30,

    'commands' => [
        'flush' => '*/15 * * * *',
        're_index'=> '*/30 * * * *'
    ],
];
