<?php

namespace App\Services\Simulators\V1\StateManagers\Caretakers;


use App\Services\Simulators\V1\StateManagers\Interfaces\Memento;

class StateCaretaker
{
    private $redis;
    private $redisKey;

    public function __construct(Client $redis, string $entityName)
    {
        $this->redis = $redis;
        $this->redisKey = $entityName . '_state_mementos';
    }

    public function addMemento(Memento $memento): void
    {
        $this->redis->rpush($this->redisKey, serialize($memento));
    }

    public function getMemento(int $index): ?Memento
    {
        $memento = $this->redis->lindex($this->redisKey, $index);
        return $memento ? unserialize($memento) : null;
    }

    public function showHistory(): array
    {
        $mementos = $this->redis->lrange($this->redisKey, 0, -1);
        $result = [];
        foreach ($mementos as $memento) {
            $result[] = unserialize($memento);
        }
        return $result;
    }
}
