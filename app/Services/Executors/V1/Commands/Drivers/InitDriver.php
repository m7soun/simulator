<?php

namespace App\Services\Executors\V1\Commands\Drivers;

use App\Services\Executors\V1\Commands\ProcessTemplate;
use App\Services\Executors\V1\Interfaces\Command;
use App\Services\Executors\V1\Interfaces\Driver;
use App\Services\Executors\V1\Interfaces\Templatable;
use App\Services\Executors\V1\Invokers\FunctionInvoker;
use App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver as DriverEntity;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver as DriverAbstract;


class InitDriver implements Command, Driver
{
    protected DriverEntity $driver;

    public function __construct(DriverEntity $driver)
    {
        $this->setDriver($driver);
    }

    public function execute()
    {
        $this->driver->setDriverId($this->driver->getDriverDetails()['id']);
        $this->driver->setDriverName($this->driver->getDriverDetails()['driver']);
        $this->driver->setLoginUsername($this->driver->getDriverDetails()['user']['login']);
        $this->driver->setLoginPassword(config('services.simulator.v1.drivers.defaults.drivers.basic.password'));
        $this->driver->setImsi($this->driver->getDriverDetails()['imsi']);
        $this->driver->setClientId($this->driver->getDriverDetails()['client_id']);
        $this->driver->setStatus(DriverAbstract::STATUS_NOT_AVAILABLE);
        $this->driver->setIsMoving(false);
        if ($this->driver instanceof Templatable) {
            $this->driver->setTemplate(
                FunctionInvoker::invoke(new ProcessTemplate($this->driver))
            );
        }
        error_log("PID - " . getmypid() . " - driver " . $this->driver->getDriverId() . " initialized");

    }

    public function setDriver(DriverEntity $driver)
    {
        $this->driver = $driver;
    }
}
