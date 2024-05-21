<?php

namespace App\Services\Simulators\V1\Actions\Drivers\MovementStrategies;


use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Interfaces\MovementStrategy;

class Movement
{

    protected MovementStrategy $movementStrategy;

    public function __construct()
    {

    }

    public function setMovementStartegy(MovementStrategy $strategy)
    {
        $this->movementStrategy = $strategy;
        return $this;
    }

    public function move()
    {
        $this->movementStrategy->move();
    }
}
