<?php

namespace App\Services\Threads\V1\Threadables\Drivers\Authentications;

use App\Services\Loggers\V1\Facades\Interfaces\Logger;
use App\Services\Loggers\V1\Facades\Logging;
use App\Services\Threads\V1\Adapters\Interfaces\Thread;
use App\Services\Threads\V1\Threadables\Interfaces\Pcntl;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;
use App\Services\Loggers\V1\Adapters\Interfaces\Logger as LoggerAdapter;

class RefreshToken implements Threadable, Pcntl
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
            // $args[1] is the action [RefreshTokenAction] and $args[0] is the entity [Driver]
            $this->prerun();
            $driverId = $args[0]->getDriverId();
            $refreshTokenAction = $args[1];
            $refreshTokenExpiresIn = $args[0]->getRefreshTokenExpiresIn();
            // Convert the Unix timestamp to a date string
            $nextRunDate = date("Y-m-d H:i:s", $refreshTokenExpiresIn);
            while (true) {
                $now = time();
                if ($now >= $refreshTokenExpiresIn) {
                    // It's time to execute the action
                    $refreshTokenAction->execute();

                    // Recalculate the next run time
                    $refreshTokenExpiresIn = $args[0]->getRefreshTokenExpiresIn();
                    $nextRunDate = date("Y-m-d H:i:s", $refreshTokenExpiresIn);
                }

                // Calculate the time to sleep until the next run
                $timeToSleep = $refreshTokenExpiresIn - $now;

                if ($timeToSleep > 0) {
                    usleep($timeToSleep);
                }
            }
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
        return 1;
    }

    public function getWaitInterval(): int
    {
        return config('services.threads.v1.config.defaults.basic_driver.wait_interval');
    }
}
