<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Orders;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Orders\Pickup;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver as DriverAbstract;
use App\Services\Simulators\V1\Entities\Interfaces\Assignable;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;


class PickupAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity&Assignable $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        error_log("PID: " . getmypid() . "Driver: " . $this->getEntity()->getDriverId() . " -  pickup action started");

        while (true) {

            if ($this->canExecute()) {
                error_log("PID: " . getmypid() . "Driver: " . $this->getEntity()->getDriverId() . " -  pickup action can be executed");

                $pickup = Client::create(new Pickup($this->getEntity()))->call();
                if ($pickup['status'] == 1) {
                    $this->getEntity()->getOrder()->setPickedUp(true);
                    $this->getEntity()->setStatus(DriverAbstract::STATUS_IN_PROGRESS);
                    error_log("PID: " . getmypid() . "Driver: " . $this->getEntity()->getDriverId() . " -  pickup action executed");

                    sleep(config('services.simulator.v1.orders.defaults.pickup_wait_time'));
                    break;
                }
            }

            sleep(3);
        }

    }

    public function canExecute(): bool
    {
        $date = $this->getEntity()->getEnterEndLocationDatetime();

        if ($date) {
            error_log("PID: " . getmypid() . "Driver: " . $this->getEntity()->getDriverId() . " -  pickup date is :" . $date->format('Y-m-d H:i:s'));
        }

        if (is_null($date)) {
            return false;
        }

        if (!is_null($date) && $date->diff(new \DateTime())->s >= 30) {
            return true;
        }
        return false;
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
