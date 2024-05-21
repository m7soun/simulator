<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Authentication;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Authentication\ShiftOff;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class ShiftOffAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {

        sleep(5);

        $dubaiTimeZone = new \DateTimeZone('Asia/Dubai');
        $shiftStartTime = $this->getEntity()->getShiftStart();
        $shiftEndTime = $this->getEntity()->getShiftEnd();

        $now = new \DateTime('now', $dubaiTimeZone);
        $shiftStart = new \DateTime($now->format('Y-m-d') . ' ' . $shiftStartTime, $dubaiTimeZone);
        $shiftEnd = new \DateTime($now->format('Y-m-d') . ' ' . $shiftEndTime, $dubaiTimeZone);

        $entity = $this->getEntity();
        $order = $entity->getOrder();

        if (($now < $shiftStart || $now > $shiftEnd) && is_null($order)) {
            Client::create(new ShiftOff($this->getEntity()))->call();
            error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " shift off");
            $this->getEntity()->setStatus(Driver::STATUS_NOT_AVAILABLE);
        }
    }

    public
    function addPrerequisite(Action $action): void
    {
        $this->prerequisites[] = $action;
    }

    public
    function hasPrerequisitesMet(): bool
    {
        return true;
    }

    public
    function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public
    function getEntity(): Entity
    {
        return $this->entity;
    }

    public
    function isThreadable(): bool
    {
        return false;
    }

    public
    function getThreadable(): Threadable|null
    {
        return null;
    }
}
