<?php

namespace App\Services\Health\V1\Interfaces;

/**
 * Interface HealthCheckInterface
 * @package App\Services\Health\V1\Interfaces
 */
interface HealthCheckInterface
{
    public function check(): array;
}
