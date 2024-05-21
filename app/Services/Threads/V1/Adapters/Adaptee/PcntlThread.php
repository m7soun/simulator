<?php

namespace App\Services\Threads\V1\Adapters\Adaptee;

use App\Services\Loggers\V1\Facades\Interfaces\Logger;
use App\Services\Loggers\V1\Facades\Logging;
use App\Services\Threads\V1\Adapters\Interfaces\Thread;
use App\Services\Threads\V1\Threadables\Interfaces\Pcntl;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;
use App\Services\Loggers\V1\Adapters\Interfaces\Logger as LoggerAdapter;
use Illuminate\Support\Facades\Storage;

class PcntlThread implements Thread
{
    private $pids = [];

    private string $key;
    private int $maxPids;

    public function __construct(private Threadable&Pcntl $threadable, private Logger|LoggerAdapter|null $logger = null)
    {
        error_log("PID - MASTER : " . getmypid() . " - PcntlThread: Constructed");
        $this->logger = $logger ?? new Logging();


        $this->setMaxPids();

        $this->threadable->setThread($this);
    }

    public function startListening(): void
    {
        pcntl_signal(SIGCHLD, function ($signo) {
            while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
                error_log("PID - MASTER : " . getmypid() . " - PcntlThread: Child process $pid exited with status $status");
                unset($this->pids[$pid]);
            }
        });
    }

    public function setMaxPids($maxPids = null): void
    {
        if ($maxPids === null) {
            $maxPids = $this->threadable->getMaxPids();
        }

        error_log("PID - MASTER : " . getmypid() . " - PcntlThread: setting max pids to: " . $maxPids);

        $this->maxPids = $maxPids;
    }

    public function run(...$args)
    {
        while (count($this->pids) >= $this->maxPids) {
            error_log("PID - MASTER : " . getmypid() . " - PcntlThread: max pids reached, waiting for " . $this->threadable->getWaitInterval() . " seconds");
            sleep($this->threadable->getWaitInterval());
        }

        $pid = pcntl_fork();

        if ($pid === -1) {
            // Handle fork failure
            die('Fork failed.');
        } elseif ($pid === 0) {
            // Child process
            $this->threadable->run($args);
            // Exit gracefully
            exit(0);
        } else {
            // Parent process
            $this->setPid($pid, $this->key);
        }
    }

    public function setKey($key): void
    {
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setPid($pid, $key): void
    {
        error_log("PID - MASTER : " . getmypid() . " - PcntlThread: setting pid: " . $pid . " for key: " . $key);
        $this->pids[$pid] = $key;
    }

    public function killPid($pid): void
    {
        posix_kill($pid, SIGTERM);
    }

    public function getPid(): int
    {
        return getmypid();
    }

    public function getRunningPids(): array
    {
        return $this->pids;
    }

    public function getRunningEntities(): array
    {
        return array_values($this->pids);
    }

    public function getRunningPidsIds(): array
    {
        return array_keys($this->pids);
    }

    public function getPidByEntity($entity): int
    {
        return array_search($entity, $this->pids);
    }

    public function killAllPids(): void
    {
        foreach ($this->pids as $pid => $entity) {
            $this->killPid($pid);
        }
    }

    public function clearSharedMemories($dir): void
    {
        error_log("PID - MASTER : " . getmypid() . " - PcntlThread: clearing shared memories for dir: " . $dir);

        $dir = 'SharedMemory/' . $dir;
        $files = Storage::files($dir);

        foreach ($files as $file) {
            Storage::delete($file);
        }
    }
}
