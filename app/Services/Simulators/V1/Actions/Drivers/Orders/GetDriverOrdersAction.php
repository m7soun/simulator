<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Orders;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\LoginOutAction;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\ShiftOffAction;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Assignable;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;
use App\Services\Requests\V1\Templates\Drivers\Orders\GetDriverOrders;

class GetDriverOrdersAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity&Assignable $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " get driver orders action");

        while (true) {
            (new ShiftOffAction($this->getEntity()))->execute();
            (new LoginOutAction($this->getEntity()))->execute();

            $getDriverOrders = Client::create(new GetDriverOrders($this->getEntity()))->call();
            if ($getDriverOrders) {
                error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " found order");

                $this->getEntity()->setOrder($getDriverOrders);
                $this->getEntity()->setEnterEndLocationDatetime(null);

                sleep(5);
                break;
            } else {
                error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " no orders found");
                sleep(10);
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
}
