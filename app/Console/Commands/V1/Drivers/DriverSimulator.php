<?php

namespace App\Console\Commands\V1\Drivers;

use App\Services\Drivers\V1\DriversService;
use App\Services\Loggers\V1\Facades\Logging as Logger;
use App\Services\Teams\V1\TeamsService;
use App\Services\Threads\V1\Adapters\Adaptee\PcntlThread;
use App\Services\Threads\V1\Threadables\Drivers\BasicDriver;
use Illuminate\Console\Command;

class DriverSimulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'simulate drivers (basic drivers)';

    public function __construct(private Logger $logger)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $dubaiTimeZone = new \DateTimeZone('Asia/Dubai');
            $shiftStartTime = config('services.simulator.v1.drivers.defaults.drivers.basic.shift_start');
            $shiftEndTime = config('services.simulator.v1.drivers.defaults.drivers.basic.shift_end');

            $usedDrivers = [];

            while (true) {
                $now = new \DateTime('now', $dubaiTimeZone);
                $shiftStart = new \DateTime($now->format('Y-m-d') . ' ' . $shiftStartTime, $dubaiTimeZone);
                $shiftEnd = new \DateTime($now->format('Y-m-d') . ' ' . $shiftEndTime, $dubaiTimeZone);

                if ($now < $shiftStart || $now > $shiftEnd) {
                    $this->info('This script can only be run between 8 am and 8 pm in Dubai time.');
                    $usedDrivers = [];
                    sleep(3600);
                }

                error_log("PID - MASTER : " . getmypid() . " - driver simulator started");
                $thread = new PcntlThread(new BasicDriver());
                $thread->clearSharedMemories('Drivers');

                $drivers = DriversService::getDriversByTeamId(TeamsService::getEnabledTeamsForDriversSimulation())->toArray();
                foreach ($drivers as $driver) {
                    if (!in_array($driver['id'], $usedDrivers)) {
                        error_log("PID - MASTER : " . getmypid() . " - driver " . $driver['id'] . " creating");
                        $usedDrivers[] = $driver['id'];
                        $thread->setKey($driver['id']);
                        $thread->run($driver);
                        error_log("PID - MASTER : " . getmypid() . " - driver " . $driver['id'] . " created");
                    } else {
                        error_log("PID - MASTER : " . getmypid() . " - driver " . $driver['id'] . " already running");
                    }
                }
                sleep(config('services.simulator.v1.drivers.loop.interval'));
            }
        } catch (\Exception $e) {
            error_log("PID - MASTER : " . getmypid() . " - " . $e->getMessage());
            sleep(30);
            $this->handle();
        }
    }
}
