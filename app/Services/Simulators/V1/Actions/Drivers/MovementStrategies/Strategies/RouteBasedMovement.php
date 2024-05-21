<?php

namespace App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Strategies;

use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Interfaces\MovementStrategy;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Moveable;

class RouteBasedMovement implements MovementStrategy
{
    private Entity&Driver $driver;

    public function __construct(Entity&Driver&Moveable $driver)
    {
        $this->driver = $driver;
    }

    public function move()
    {

        error_log("PID - " . getmypid() . " - " . $this->driver->getDriverId() . " route based movement strategy");

        $directions = $this->driver->getRoute();
        $currentLocation = array_shift($directions);

        if (is_null($currentLocation)) {
            $this->driver->setIsMoving(false);
            return;
        }

        error_log(count($directions) . " - ------" . $currentLocation['lat'] . " - " . $currentLocation['lng']);

        $this->driver->setLatitude($currentLocation['lat']);
        $this->driver->setLongitude($currentLocation['lng']);

        $this->driver->setRoute($directions);
    }
}
