<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Authentication;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Authentication\Login;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class LoginAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        sleep(5);
        if ($this->getEntity()->isLoggedIn()) {
            error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverName() . " already logged in");
            return;
        }
        $driverLoginResponse = Client::create(new Login($this->getEntity()))->call();

        error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverName() . " driver id : " . $this->getEntity()->getDriverId() . " login response : " . json_encode($driverLoginResponse));

        if (!isset($driverLoginResponse['stores_list'])) {
            error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverName() . " driver id : " . $this->getEntity()->getDriverId() . " login failed or driver is not dedicated");
            exit(0);
        }

        $this->getEntity()->setDriverLoginResponse($driverLoginResponse);


        $this->getEntity()->setDriverPickup($this->getEntity()->getDriverLoginResponse()['stores_list']);
        $this->getEntity()->setDriverPickupLatitude($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['latitude']);
        $this->getEntity()->setDriverPickupLongitude($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['longitude']);
        $this->getEntity()->setEndLatitude(($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['latitude']));
        $this->getEntity()->setEndLongitude(($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['longitude']));

        $this->getEntity()->setLatitude(($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['latitude']));
        $this->getEntity()->setLongitude(($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['longitude']));
        $this->getEntity()->setStartLatitude(($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['latitude']));
        $this->getEntity()->setStartLongitude(($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['longitude']));


        $this->getEntity()->setStoreRadius($this->getEntity()->getDriverLoginResponse()['stores_list'][0]['radius']);
        $this->getEntity()->setAccessToken($this->getEntity()->getDriverLoginResponse()['access_token']);
        $this->getEntity()->setRefreshToken($this->getEntity()->getDriverLoginResponse()['refresh_token']);
        $this->getEntity()->setRefreshTokenExpiresIn($this->getEntity()->getDriverLoginResponse()['expires']);
        $this->getEntity()->setDriverName($this->getEntity()->getDriverLoginResponse()['settings']['driver_name']);

        error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverName() . " driver id : " . $this->getEntity()->getDriverId() . " logged in");
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
