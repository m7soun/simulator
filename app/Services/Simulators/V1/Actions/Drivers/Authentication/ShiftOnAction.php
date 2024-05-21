<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Authentication;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Authentication\ShiftOn;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class ShiftOnAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        sleep(5);
        if ($this->getEntity()->isShiftOn()) {
            error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " already shifted on");
            return;
        }
        $this->getEntity()->setShiftOnResponse(Client::create(new ShiftOn($this->entity))->call());
        $this->getEntity()->setStatus(Driver::STATUS_AVAILABLE);

        error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " shifted on");
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
