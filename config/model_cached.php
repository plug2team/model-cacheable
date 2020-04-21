<?php

return [
    'tag' => [
        /*
         |
         | Determines that the cache does not expire
         |
         */
        'permanent' => true,

        /*
         |
         | Determine time in minutes if the cache expires.
         |
         */
        'time' => 90
    ],

    'cache_name' => 'cached',

    /*
     |
     | List of keys reserved of package.
     |
     */
    'reserved_keys' => [
        'indexes',
        'cursor'
    ],

    /*
     |
     | Assume that the information property is used as the key
     |
     */
    'key_attribute' => 'id',

    /*
     |
     | List of auxiliary commands
     |
     */
    'commands' => [
        'flush' => '*/15 * * * *',
        're_index'=> '*/30 * * * *'
    ],
];
