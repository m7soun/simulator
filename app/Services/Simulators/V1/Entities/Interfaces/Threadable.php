<?php

namespace App\Services\Simulators\V1\Entities\Interfaces;

interface Threadable
{
    public function setTheadKey(string $key): void;

    public function getThreadKey(): string;
}
