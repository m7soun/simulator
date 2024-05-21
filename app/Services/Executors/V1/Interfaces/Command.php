<?php

namespace App\Services\Executors\V1\Interfaces;


use App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver;

interface Command
{
    public function execute();
}
