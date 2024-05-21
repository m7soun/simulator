<?php

namespace App\Services\Simulators\V1\StateManagers\Concretes;

use App\Services\Simulators\V1\StateManagers\Interfaces\Memento;

class ConcreteMemento implements Memento
{
    private $state;
    private $date;

    public function __construct(array $state)
    {
        $this->state = $state;
        $this->date = date('Y-m-d H:i:s');
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getName(): string
    {
        return $this->date . " / Entity State";
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
