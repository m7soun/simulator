<?php

namespace App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Interfaces;

use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Moveable;

interface MovementStrategy
{

    public function __construct(Entity&Moveable&Driver $driver);

    public function move();
}
