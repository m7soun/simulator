<?php

namespace App\Services\Loggers\V1\Adapters\Interfaces;
interface Logger
{
    public function error(string $message);

    public function warning(string $message);

    public function info(string $message);

    public function debug(string $message);

    public function cloneLogger();
}
