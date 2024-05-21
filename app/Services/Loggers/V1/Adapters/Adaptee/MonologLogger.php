<?php

namespace App\Services\Loggers\V1\Adapters\Adaptee;

use App\Services\Loggers\V1\Adapters\Interfaces\Logger;
use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;

class MonologLogger implements Logger
{
    private Monolog $logger;

    public function __construct()
    {
        // Create a Monolog logger instance with a name (e.g., 'my_logger')
        $this->logger = new Monolog('SIMULATOR_LOGGER');

        // Add a StreamHandler to log messages to stdout (standard output)
        $this->logger->pushHandler(new StreamHandler('php://stdout', Monolog::DEBUG));
    }

    /**
     * Log an error message.
     *
     * @param string $message The error message to log.
     */
    public function error(string $message): void
    {
        $this->logger->error($message);
    }

    /**
     * Log a warning message.
     *
     * @param string $message The warning message to log.
     */
    public function warning(string $message): void
    {
        $this->logger->warning($message);
    }

    /**
     * Log an informational message.
     *
     * @param string $message The informational message to log.
     */
    public function info(string $message): void
    {
        $this->logger->info($message);
    }

    /**
     * Log a debug message.
     *
     * @param string $message The debug message to log.
     */
    public function debug(string $message): void
    {
        $this->logger->debug($message);
    }

    /**
     * Clone the logger.
     *
     * @return MonologLogger A cloned instance of the logger.
     */
    public function cloneLogger(): MonologLogger
    {
        return clone $this;
    }
}
