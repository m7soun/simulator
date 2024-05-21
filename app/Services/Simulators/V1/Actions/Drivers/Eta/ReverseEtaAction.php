<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Eta;

use App\Services\Executors\V1\Commands\Drivers\PrepareRoute;
use App\Services\Executors\V1\Invokers\FunctionInvoker;
use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Eta\Eta;
use App\Services\Requests\V1\Templates\Drivers\Eta\ReverseEta;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Assignable;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;

use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class ReverseEtaAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity&Assignable $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " eta action");

        $eta = Client::create(new ReverseEta($this->getEntity()))->call();

        if ($eta['directions'][0]['lat'] != $this->getEntity()->getLatitude() || $eta['directions'][0]['lng'] != $this->getEntity()->getLongitude()) {
            array_unshift($eta['directions'], [
                'lat' => $this->getEntity()->getLatitude(),
                'lng' => $this->getEntity()->getLongitude()
            ]);
        }

        $lastIndex = count($eta['directions']) - 1;

        if ($eta['directions'][$lastIndex]['lat'] != $this->getEntity()->getEndLatitude() || $eta['directions'][$lastIndex]['lng'] != $this->getEntity()->getEndLongitude()) {
            $eta['directions'][] = [
                'lat' => $this->getEntity()->getEndLatitude(),
                'lng' => $this->getEntity()->getEndLongitude()
            ];
        }

        $this->getEntity()->setRoute($eta['directions']);
        $this->getEntity()->setEta($eta['eta']);
        $this->getEntity()->setRouteDistance($eta['distance']);

        FunctionInvoker::invoke(new PrepareRoute($this->getEntity()));
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
