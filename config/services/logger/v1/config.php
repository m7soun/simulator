<?php

return [
    'defaults' => [
        'timezone' => 'UTC',
        'adapter' => App\Services\Loggers\V1\Adapters\Adaptee\MonologLogger::class,
        'decorator' => App\Services\Loggers\V1\Decorators\TemplateBasedDecorators\ConfigTemplatesLoggerDecorator::class,
        'facade' => App\Services\Loggers\V1\Facades\Logging::class,
        'workflow' => [
            'facade' => \App\Services\Loggers\V1\Facades\Adapters\Adaptee\ConnectToAdapter::class,
            'adapter' => \App\Services\Loggers\V1\Adapters\Adaptee\MonologLogger::class,
            'decorator' => \App\Services\Loggers\V1\Decorators\TemplateBasedDecorators\ConfigTemplatesLoggerDecorator::class
        ]
    ],
    'templates' => [

        'v1' => [
            'timezone' => 'defaults.timezone',
            'error' => 'Error: {{message}} {{getCurrentTimeInGst}}',
            'warning' => 'Warning: {{message}} {{getCurrentTimeInGst}}',
            'info' => 'Info: {{message}} {{getCurrentTimeInGst}}',
            'debug' => 'Debug: {{message}} {{getCurrentTimeInGst}}',
        ]
    ],
];
