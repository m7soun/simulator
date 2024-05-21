<?php

namespace App\Services\Threads\V1\Adapters\Interfaces;

use App\Services\Loggers\V1\Facades\Interfaces\Logger;
use App\Services\Threads\V1\Threadables\Interfaces\Pcntl;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;
use App\Services\Loggers\V1\Adapters\Interfaces\Logger as LoggerAdapter;

interface Thread
{
    public function __construct(Threadable&Pcntl $threadable, Logger|LoggerAdapter|null $logger = null);

    public function startListening(): void;

    public function setMaxPids($maxPids = null): void;

    public function setPid($pid, $key): void;

    public function run(...$args);

    public function killPid($pid): void;

    public function getPid(): int;

    public function getRunningPids(): array;

    public function getRunningEntities(): array;

    public function getRunningPidsIds(): array;

    public function getPidByEntity($entity): int;

    public function killAllPids(): void;

    public function setKey($key): void;

    public function getKey(): string;
}
