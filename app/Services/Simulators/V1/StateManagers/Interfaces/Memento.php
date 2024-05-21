<?php

namespace App\Services\Simulators\V1\StateManagers\Interfaces;

interface Memento
{
    public function getState(): array;

    public function getName(): string;

    public function getDate(): string;
}
