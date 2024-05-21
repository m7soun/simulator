<?php

namespace App\Services\Threads\V1\Threadables\Interfaces;

interface Pcntl
{
    public function getMaxPids(): int;

    public function getWaitInterval(): int;
}
