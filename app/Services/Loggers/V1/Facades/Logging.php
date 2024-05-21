<?php

namespace App\Services\Loggers\V1\Facades;


use App\Services\Loggers\V1\Facades\Adapters\Interfaces\Connection;
use App\Services\Loggers\V1\Facades\Interfaces\Logger as FacadesLoggerInterface;

class Logging implements FacadesLoggerInterface
{
    private $decorator = null;

    public function __construct(private ?Connection $connection = null)
    {
        if (is_null($this->connection)) {
            $this->connection = $this->iniDefaults();
        }
    }

    public function iniDefaults()
    {
        $connection = app()->make(config('services.logger.v1.config.defaults.workflow.facade'));

        return $connection;
    }

    public function error(string $message): void
    {
        $this->connection->error($message);
    }

    public function warning(string $message): void
    {
        $this->connection->warning($message);
    }

    public function info(string $message): void
    {
        $this->connection->info($message);
    }

    public function debug(string $message): void
    {
        $this->connection->debug($message);
    }
}

