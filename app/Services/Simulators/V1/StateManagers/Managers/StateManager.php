<?php

namespace App\Services\Simulators\V1\StateManagers\Managers;

use App\Services\Simulators\V1\StateManagers\Interfaces\Memento;

class StateManager
{
    private $state = [];
    private $redis;
    private $redisKey;

//    public function __construct(Client $redis, string $entityName)
//    {
//        $this->redis = $redis;
//        $this->redisKey = $entityName . '_state';
//    }

    public function setState(array $state): void
    {
        $this->state = $state;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function saveToMemento(): Memento
    {
        $memento = new ConcreteMemento($this->state);
        $this->redis->rpush($this->redisKey, serialize($memento));
        return $memento;
    }

    public function restoreFromMemento(Memento $memento): void
    {
        $this->state = $memento->getState();
    }
}
