<?php

namespace App\Services\Health\V1\Checks;

use App\Services\Health\V1\Interfaces\HealthCheckInterface;
use App\Services\Loggers\V1\Facades\Interfaces\Logger as FacadesLoggerInterface;
use App\Services\Loggers\V1\Adapters\Interfaces\Logger as AdapterLoggerInterface;
use App\Services\Loggers\V1\Facades\Logging;
use Illuminate\Support\Facades\DB;

class DatabaseHealthCheck implements HealthCheckInterface
{
    public function __construct(private FacadesLoggerInterface|AdapterLoggerInterface|null $logger = null)
    {
        if (is_null($this->logger)) {
            $this->logger = new Logging();
        }
    }

    public function check(): array
    {
        error_log("PID - MASTER : " . getmypid() . " - DatabaseHealthCheck: check");
        try {
            DB::connection()->getPdo();
            error_log("PID - MASTER : " . getmypid() . " - DatabaseHealthCheck: check - OK");
            return ['name' => 'Database', 'status' => 'OK'];
        } catch (\Exception $e) {
            error_log("PID - MASTER : " . getmypid() . " - DatabaseHealthCheck: check - ERROR - " . $e->getMessage());
            return ['name' => 'Database', 'status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }
}
