<?php

namespace App\Services\Loggers\V1\Facades\Adapters\Interfaces;

interface Connection
{
    public function connect();

    public function error(string $message): void;

    public function warning(string $message): void;

    public function info(string $message): void;

    public function debug(string $message): void;

}
