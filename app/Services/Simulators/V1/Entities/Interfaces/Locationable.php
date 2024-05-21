<?php

namespace App\Services\Simulators\V1\Entities\Interfaces;

use App\Services\Simulators\V1\Entities\Interfaces\Locations\Location;

interface Locationable
{
    public function setLocation(Location $location): void;

    public function getLocation(): Location;
}
