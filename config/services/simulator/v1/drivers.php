<?php


return [
    'loop' => [
        'interval' => env('DRIVER_SIMULATOR_LOOP_INTERVAL', 1800),
        'teams' => env('DRIVER_SIMULATOR_LOOP_TEAMS', 69339201)
    ],
    'defaults' => [
        'drivers' => [
            'basic' => [
                'entity_name' => 'simulator:basic:driver:{{getDriverId:driver}}:state',
                'password' => env('BASE_DRIVER_DEFAULT_PASSWORD', '123'),
                'shift_start' => env('BASE_DRIVER_DEFAULT_SHIFT_START', '08:00:00'),
                'shift_end' => env('BASE_DRIVER_DEFAULT_SHIFT_END', '20:00:00'),
            ]
        ]
    ]
];
