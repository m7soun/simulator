<?php

namespace App\Services\Simulators\V1\Entities\Interfaces\Locations;

interface Location
{
    public function setLatitude(float $latitude): void;

    public function setLongitude(float $longitude): void;

    public function getLatitude(): float;

    public function getLongitude(): float;

    public function setAddress(string $address): void;

    public function getAddress(): string;
}
