<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Move;


use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Eta\ReverseEta;
use App\Services\Requests\V1\Templates\Drivers\Geo\Geo;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\LoginOutAction;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\ShiftOffAction;
use App\Services\Simulators\V1\Actions\Drivers\Eta\EtaAction;
use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Movement;
use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Strategies\RouteBasedMovement;
use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Strategies\StraightLineMovement;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Moveable;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;
use App\Services\Threads\V1\Threadables\Drivers\DriverMovement as UnassignedDriverMovementThreadable;

class WaitForMoversAction implements Action
{
    protected Driver&Entity $entity;

    protected $thread;

    public function __construct(Driver&Entity&Moveable $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        while (true) {
            if ($this->getEntity()->isMoving()) {
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  Driver is moving , wait for him to finish moving");
                sleep(5);
            } else {
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  Driver is not moving , start moving");

                break;
            }
        }


    }


    public function addPrerequisite(Action $action): void
    {
        $this->prerequisites[] = $action;
    }

    public function hasPrerequisitesMet(): bool
    {
        return true;
    }

    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function isThreadable(): bool
    {
        return false;
    }

    public function getThreadable(): Threadable|null
    {
        return null;
    }

    public function setThread($thead): void
    {
        $this->thread = $thead;
    }
}
