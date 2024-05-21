<?php

namespace App\Services\Simulators\V1\Entities\Pois\BasicPois;

use App\Services\Simulators\V1\Entities\Interfaces\Locationable;
use App\Services\Simulators\V1\Entities\Interfaces\Locations\Location;
use App\Services\Simulators\V1\Entities\Interfaces\Pois\Poi;

class BasicPoi implements Poi, Locationable
{
    protected int $poiId;

    protected int $radius;

    protected Location $location;

    public function setPoiId(int $poiId): void
    {
        $this->poiId = $poiId;
    }

    public function getPoiId(): int
    {
        return $this->poiId;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setRadius(int $radius): void
    {
        $this->radius = $radius;
    }

    public function getRadius(): int
    {
        return $this->radius;
    }
}
