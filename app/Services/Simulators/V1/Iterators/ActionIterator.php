<?php

namespace App\Services\Simulators\V1\Iterators;

use App\Services\Simulators\V1\Actions\Interfaces\Action;
use Iterator;

class ActionIterator implements Iterator
{
    private $actionClasses = [];
    private $position = 0;

    public function __construct(array $actionClasses)
    {
        $this->actionClasses = $actionClasses;
    }

    public function current(): Action
    {
        $actionClass = $this->actionClasses[$this->position];
        return new $actionClass();
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): Action
    {
        $nextPosition = $this->position + 1;

        // Find the next valid position
        while ($nextPosition < count($this->actionClasses) && !$this->isValidPosition($nextPosition)) {
            $nextPosition++;
        }

        if ($nextPosition < count($this->actionClasses)) {
            $this->position = $nextPosition;
        }

        return $this->current();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->isValidPosition($this->position);
    }

    private function isValidPosition($position): bool {
        return isset($this->actionClasses[$position]) && $position >= 0;
    }
}
