<?php

namespace App\Services\Loggers\V1\Facades\Adapters\Adaptee;

use App\Services\Loggers\V1\Adapters\Interfaces\Logger;
use App\Services\Loggers\V1\Facades\Adapters\Interfaces\Connection;

class ConnectToAdapter implements Connection
{
    public function __construct(private ?Logger $logger = null)
    {
        if (is_null($this->logger)) {
            $this->logger = $this->iniDefaults();
        }
    }

    public function iniDefaults()
    {
        $logger = app()->make(config('services.logger.v1.config.defaults.workflow.adapter'));

        return $logger;
    }

    public function connect()
    {
        // TODO: Implement connect() method.
    }

    public function error(string $message): void
    {
        $this->logger->error($message);
    }

    public function warning(string $message): void
    {
        $this->logger->warning($message);
    }

    public function info(string $message): void
    {
        $this->logger->info($message);
    }

    public function debug(string $message): void
    {
        $this->logger->debug($message);
    }
}
