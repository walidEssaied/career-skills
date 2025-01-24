<?php

return [
    'python_path' => env('PYTHON_PATH', '/usr/local/bin/python'),
    'script_path' => env('ML_SCRIPT_PATH', base_path('ml/recommender.py')),
    'host' => env('ML_SERVICE_HOST', 'ml'),
    'port' => env('ML_SERVICE_PORT', '5000'),
];
