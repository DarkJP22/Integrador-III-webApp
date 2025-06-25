<?php

return [
    'slot_durations' => [
        [
            'value' => '00:10:00',
            'valueAsMinutes' => 10,
            'text' => '10 min'
        ],
        [
            'value' => '00:20:00',
            'valueAsMinutes' => 20,
            'text' => '20 min'
        ],
        [
            'value' => '00:30:00',
            'valueAsMinutes' => 30,
            'text' => '30 min'
        ],
        [
            'value' => '00:45:00',
            'valueAsMinutes' => 45,
            'text' => '45 min'
        ],
        [
            'value' => '01:00:00',
            'valueAsMinutes' => 60,
            'text' => '1 h'
        ],
        [
            'value' => '01:30:00',
            'valueAsMinutes' => 90,
            'text' => '1:30 h'
        ],
        [
            'value' => '02:00:00',
            'valueAsMinutes' => 120,
            'text' => '2 h'
        ]
    ],
    'pusher_app_key' => env('PUSHER_APP_KEY'),
    'pusher_app_cluster' => env('PUSHER_APP_CLUSTER'),
];