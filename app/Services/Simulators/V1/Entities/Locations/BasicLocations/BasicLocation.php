<?php

namespace App\Services\Simulators\V1\Entities\Locations\BasicLocations;

use App\Services\Simulators\V1\Entities\Interfaces\Locations\Location;

class BasicLocation implements Location
{

    protected float $latitude;
    protected float $longitude;
    protected string $address;

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
