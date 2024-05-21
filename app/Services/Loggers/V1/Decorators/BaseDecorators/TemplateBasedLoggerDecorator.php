<?php

namespace App\Services\Loggers\V1\Decorators\BaseDecorators;

use App\Services\Loggers\V1\Adapters\Interfaces\Logger;

abstract class TemplateBasedLoggerDecorator implements Logger
{
    public function __construct(protected Logger $logger)
    {
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

    public function cloneLogger(): TemplateBasedLoggerDecorator|static
    {
        return clone $this;
    }

    abstract public function getTemplate(): string;

    public function extractActions($template)
    {
        $actions = [];
        preg_match_all('/\[(.*?)\]/', $template, $actions);
        return $actions[1];

    }
}
