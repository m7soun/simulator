<?php

namespace App\Services\Simulators\V1\Entities\Interfaces;

use App\Services\Simulators\V1\Entities\Interfaces\Pois\Poi;

interface Poiable
{
    public function setPoi(Poi $poi): void;

    public function getPoi(): Poi;

}
