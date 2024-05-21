<?php

namespace App\Services\Simulators\V1\Actions\Interfaces;

use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

interface Action
{
    public function execute($data = null): void;

    public function addPrerequisite(Action $action): void;

    public function hasPrerequisitesMet(): bool;

    public function setEntity(Entity $entity): void;

    public function getEntity(): Entity;

    public function isThreadable(): bool;


    public function getThreadable(): Threadable|null;
}
