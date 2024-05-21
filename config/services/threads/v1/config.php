<?php

return [
    'defaults' => [
        'basic_driver' => [
            'max_pids' => env('TREADS_BASIC_DRIVER_MAX_PIDS', 5),
            'wait_interval' => env('TREADS_BASIC_DRIVER_WAIT_INTERVAL', 10)
        ]
    ]
];
