<?php

namespace App\Services\Health\V1;

use App\Services\Health\V1\Interfaces\HealthCheckInterface;
use App\Services\Loggers\V1\Facades\Interfaces\Logger as FacadesLoggerInterface;
use App\Services\Loggers\V1\Adapters\Interfaces\Logger as AdapterLoggerInterface;
use App\Services\Loggers\V1\Facades\Logging;
use function PHPUnit\Framework\isEmpty;

class HealthCheckService
{
    private $logger = null;

    public function __construct()
    {
        $this->logger = new Logging();
    }

    public function run(array $healthChecks = []): array
    {

        error_log("PID - MASTER : " . getmypid() . " - HealthCheckService: run");

        if (empty($healthChecks)) {
            $healthChecks = $this->getChecks();
        }
        $results = [];

        foreach ($healthChecks as $check) {
            $healthCheckClass = new $check();
            if ($healthCheckClass instanceof HealthCheckInterface) {
                $results[] = $healthCheckClass->check();
            } else {
                $this->logger->error("PID - MASTER : " . getmypid() . " - HealthCheckService: run - " . $check . " is not an instance of HealthCheckInterface");
            }
        }

        return $results;
    }


    public function setLogger(FacadesLoggerInterface|AdapterLoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    private function getChecks(): array
    {
        return config('services.health.v1.checks.checks');
    }
}
