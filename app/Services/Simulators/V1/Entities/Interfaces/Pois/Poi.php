<?php

namespace App\Services\Simulators\V1\Entities\Interfaces\Pois;

interface Poi
{
    public function setPoiId(int $poiId): void;

    public function getPoiId(): int;

    public function setRadius(int $radius): void;

    public function getRadius(): int;
}
