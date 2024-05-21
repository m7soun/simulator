<?php

return [
    'defaults' => [
        'request' => \app\Services\Requests\V1\Builders\Requests\Apis\Request::class,
        'mobile_proxy_base_url' => env('MOBILE_PROXY_BASE_URL', 'https://mobile-proxy.stage.lyve.global'),
        'eta_base_url' => env('ETA_BASE_URL', 'https://eta-service.stage.lyve.global'),
        'geo_base_url' => env('GEO_BASE_URL', 'https://geo-proxy.stage.lyve.global'),
        'mobile_version' => env('MOBILE_VERSION', '1.3.59'),
    ]
];
