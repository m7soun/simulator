<?php

namespace App\Services\Loggers\V1\Facades\Interfaces;

interface Logger
{
    public function error(string $message): void;

    public function warning(string $message): void;

    public function info(string $message): void;

    public function debug(string $message): void;
}
