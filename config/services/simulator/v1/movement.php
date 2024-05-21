<?php


return [
    'defaults' => [
        'arrival_distance' => env('ARRIVAL_DISTANCE', 70), // meters
        'sleep' => env('MOVEMENT_SLEEP', 1), // seconds
    ],
    'straight_line' => [
        'radius' => env('STRAIGHT_LINE_MOVEMENT_RADIUS', 1), // km
        'step' => env('STRAIGHT_LINE_MOVEMENT_STEP', 1), // km
    ]
];
