<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Authentication;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Authentication\RefreshToken;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Drivers\Authentications\RefreshToken as RefreshTokenThreadable;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class RefreshTokenAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        sleep(5);
        if ($this->entity->isLoggedIn()) {
            error_log("PID - " . getmypid() . " - " . $this->entity->getDriverId() . " already logged in");
            exit(0);
        }
        $response = Client::create(new RefreshToken($this->entity))->call();
        $this->entity->setRefreshTokenApiResponse($response);
        $this->entity->setAccessToken($this->entity->getRefreshTokenApiResponse()['access_token']);
        $this->entity->setRefreshToken($this->entity->getRefreshTokenApiResponse()['refresh_token']);
        $this->entity->setRefreshTokenExpiresIn($this->entity->getRefreshTokenApiResponse()['expires']);

        error_log("PID - " . getmypid() . " - " . $this->entity->getDriverId() . " refresh token");
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
        return true;
    }

    public function getThreadable(): Threadable|null
    {
        return new RefreshTokenThreadable();
    }
}
