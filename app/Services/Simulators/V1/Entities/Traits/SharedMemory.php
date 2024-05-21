<?php

namespace App\Services\Simulators\V1\Entities\Traits;

use Illuminate\Support\Facades\Storage as SharedMemoryStorage;

trait SharedMemory
{
    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getSharedMemory($key = null)
    {
        if (SharedMemoryStorage::exists($this->getFilePath())) {
            $sharedMemory = json_decode(SharedMemoryStorage::get($this->getFilePath()), true);
            if (is_null($key)) {
                return $sharedMemory;
            }
            return $sharedMemory[$key] ?? null;
        }

        if (is_null($key)) {
            return [];
        }
        return null;
    }

    public function setSharedMemory($key, $value): void
    {
        $sharedMemory = $this->getSharedMemory();
        $sharedMemory[$key] = $value;
        SharedMemoryStorage::put($this->getFilePath(), json_encode($sharedMemory));
    }

    public function isSharedMemoryEnabled(): bool
    {
        return true;
    }
}
