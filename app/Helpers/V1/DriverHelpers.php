<?php

if (!function_exists('getDriverId')) {
    function getDriverId(\App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver $driver)
    {
        return $driver->getDriverId();
    }
}
