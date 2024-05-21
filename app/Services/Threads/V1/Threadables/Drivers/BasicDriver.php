<?php

namespace App\Services\Threads\V1\Threadables\Drivers;

use App\Services\Loggers\V1\Facades\Interfaces\Logger;
use App\Services\Loggers\V1\Facades\Logging;
use App\Services\Simulators\V1\Facades\Simulation;
use App\Services\Simulators\V1\StateManagers\Managers\StateManager;
use App\Services\Threads\V1\Adapters\Adaptee\PcntlThread;
use App\Services\Threads\V1\Adapters\Interfaces\Thread;
use App\Services\Threads\V1\Threadables\Interfaces\Pcntl;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;
use App\Services\Simulators\V1\Entities\Drivers\BasicDrivers\BasicDriver as BasicDriverEntity;
use App\Services\Loggers\V1\Adapters\Interfaces\Logger as LoggerAdapter;

class BasicDriver implements Threadable, Pcntl
{

    protected Thread $thread;

    public function __construct(private Logger|LoggerAdapter|null $logger = null)
    {
        if (is_null($this->logger)) {
            $this->logger = new Logging();
        }
    }

    public function setThread(Thread $thread)
    {
        $this->thread = $thread;
    }

    public function run($args)
    {
        try {
            $this->prerun();
            error_log("PID : " . getmypid() . " - BasicDriver: Running with PID: " . $this->thread->getPid());
            new Simulation(new BasicDriverEntity($args[0]), new StateManager());
        } catch (\Exception $e) {
            error_log("PID - " . getmypid() . " - " . $e->getMessage() . " - " . $e->getTraceAsString());
            sleep(30);
            $this->run($args);
        }
    }

    protected function prerun()
    {
        if (!$this->thread) {
            throw new \Exception('Thread not set , use setThread() method to set thread');
        }
    }

    public function getMaxPids(): int
    {
        return config('services.threads.v1.config.defaults.basic_driver.max_pids');
    }

    public function getWaitInterval(): int
    {
        return config('services.threads.v1.config.defaults.basic_driver.wait_interval');
    }
}
