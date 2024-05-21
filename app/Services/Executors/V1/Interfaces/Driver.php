<?php

namespace App\Services\Executors\V1\Interfaces;

use App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver as DriverEntity;

interface Driver
{
    public function setDriver(DriverEntity $driver);
}
