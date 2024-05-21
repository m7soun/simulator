<?php

namespace App\Services\Simulators\V1\Entities\Drivers\BasicDrivers;


use App\Services\Executors\V1\Commands\Drivers\InitDriver;
use App\Services\Executors\V1\Interfaces\Templatable;
use App\Services\Executors\V1\Invokers\FunctionInvoker;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\LoginAction;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\LoginOutAction;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\RefreshTokenAction;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\ShiftOffAction;
use App\Services\Simulators\V1\Actions\Drivers\Authentication\ShiftOnAction;
use App\Services\Simulators\V1\Actions\Drivers\Eta\EtaAction;
use App\Services\Simulators\V1\Actions\Drivers\Eta\ReverseEtaAction;
use App\Services\Simulators\V1\Actions\Drivers\Move\MovementAction;
use App\Services\Simulators\V1\Actions\Drivers\Move\WaitForMoversAction;
use App\Services\Simulators\V1\Actions\Drivers\Orders\DropOffAction;
use App\Services\Simulators\V1\Actions\Drivers\Orders\GetDriverOrdersAction;
use App\Services\Simulators\V1\Actions\Drivers\Orders\GetJobAction;
use App\Services\Simulators\V1\Actions\Drivers\Orders\PickupAction;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver as DriverAbstract;
use App\Services\Simulators\V1\Entities\Interfaces\Assignable;
use App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Moveable;
use App\Services\Simulators\V1\Entities\Interfaces\Orders\Order;
use App\Services\Simulators\V1\Entities\Interfaces\Threadable;
use App\Services\Simulators\V1\Entities\Orders\BasicOrders\BasicOrder;
use App\Services\Simulators\V1\Entities\Traits\SharedMemory;


class BasicDriver extends DriverAbstract implements Entity, Driver, Templatable, Threadable, Assignable, Moveable
{

    use SharedMemory;

    protected string $threadKey;

    protected string $filePath = 'SharedMemory/Drivers';

    protected Order|null $order;
    protected int $orderId = 0;
    protected float $startLatitude = 0.0;
    protected float $startLongitude = 0.0;
    protected float $endLatitude = 0.0;
    protected float $endLongitude = 0.0;

    protected float $distance = 0.0;
    protected $enterEndLocationDatetime;

    protected array $route = [];

    protected int $eta = 0;

    protected float $routeDistance = 0.0;

    protected string $enterStoreRadius = "";

    protected int $storeRadius = 0;

    protected bool $killOtherMovers = false;


    public function __construct(array $driverDetails)
    {
        $this->setDriverDetails($driverDetails);
        $this->setTemplate();
        $this->initDriver();
        $this->setTheadKey($this->getDriverId());
    }

    public function getActions(): array
    {
        return [
            LoginAction::class,
            ShiftOnAction::class,
            RefreshTokenAction::class,
            WaitForMoversAction::class,
            MovementAction::class,
            GetDriverOrdersAction::class,
            GetJobAction::class,
            WaitForMoversAction::class,
            MovementAction::class,
            PickupAction::class,
            EtaAction::class,
            WaitForMoversAction::class,
            MovementAction::class,
            DropOffAction::class,
            ReverseEtaAction::class,
            ShiftOffAction::class,
            LoginOutAction::class
        ];
    }

    public function initDriver(): void
    {
        FunctionInvoker::invoke(new InitDriver($this));
    }

    public function getTemplate(): string|null
    {
        return $this->entityName;
    }

    public function setTemplate(string|null $template = null): void
    {
        if (!is_null($template)) {
            $this->entityName = $template;
        } else {
            $this->entityName = config('services.simulator.v1.drivers.defaults.drivers.basic.entity_name');
        }
    }

    public function setTheadKey(string $key): void
    {
        $this->threadKey = $key;
    }

    public function getThreadKey(): string
    {
        return $this->threadKey;
    }

    public function getFilePath(): string
    {
        return $this->filePath . '/' . $this->getDriverId() . '.json';
    }

    public function setOrder(Order|int|null $order): void
    {
        if (is_int($order)) {
            $this->order = new BasicOrder();
            $this->order->setOrderId($order);
            $this->setOrderId($order);
        } else {
            $this->order = $order;
        }
    }

    public function getOrder(): Order|null
    {
        return $this->order ?? null;
    }

    public function setOrderId(int|null $order): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('orderId', $order);
            }
            $this->orderId = $order;
        }
    }

    public function getOrderId(): int|null
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $orderId = $this->getSharedMemory('orderId');
                if (!is_null($orderId)) {
                    $this->orderId = $orderId;
                }
            }
        }
        return $this->orderId;
    }

    public function setStartLatitude(float $latitude): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('startLatitude', $latitude);
            }
        }
        $this->startLatitude = $latitude;
    }

    public function getStartLatitude(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $latitude = $this->getSharedMemory('startLatitude');
                if (!is_null($latitude)) {
                    $this->startLatitude = $latitude;
                }
            }
        }
        return $this->startLatitude;
    }

    public function setStartLongitude(float $longitude): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('startLongitude', $longitude);
            }
        }
        $this->startLongitude = $longitude;
    }

    public function getStartLongitude(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $longitude = $this->getSharedMemory('startLongitude');
                if (!is_null($longitude)) {
                    $this->startLongitude = $longitude;
                }
            }
        }
        return $this->startLongitude;
    }

    public function setEndLatitude(float $latitude): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {

                $this->setSharedMemory('endLatitude', $latitude);
            }
        }
        $this->endLatitude = $latitude;
    }

    public function getEndLatitude(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $latitude = $this->getSharedMemory('endLatitude');
                if (!is_null($latitude)) {
                    $this->endLatitude = $latitude;
                }
            }
        }
        return $this->endLatitude;
    }

    public function setEndLongitude(float $longitude): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {

                $this->setSharedMemory('endLongitude', $longitude);
            }
        }
        $this->endLongitude = $longitude;
    }

    public function getEndLongitude(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {

            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {

                $longitude = $this->getSharedMemory('endLongitude');
                if (!is_null($longitude)) {
                    $this->endLongitude = $longitude;
                }
            }
        }
        return $this->endLongitude;
    }

    public function setDistance(float $distance): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('distance', $distance);
            }
        }
        $this->distance = $distance;
    }

    public function getDistance(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $distance = $this->getSharedMemory('distance');
                if (!is_null($distance)) {
                    $this->distance = $distance;
                }
            }
        }
        return $this->distance;
    }

    public function setEnterEndLocationDatetime($date): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                // Store the date as a string in shared memory
                if (!is_null($date) && !empty($date)) {
                    $this->setSharedMemory('enterEndLocationDatetime', $date->format('Y-m-d H:i:s'));
                } else {
                    $this->setSharedMemory('enterEndLocationDatetime', "");
                }
            }
        }
        $this->enterEndLocationDatetime = $date;
    }

    public function getEnterEndLocationDatetime()
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $dateString = $this->getSharedMemory('enterEndLocationDatetime');
                if (!is_null($dateString) && !empty($dateString)) {
                    // Cast the date string to a DateTime object
                    $this->enterEndLocationDatetime = new \DateTime($dateString);
                } else {
                    $this->enterEndLocationDatetime = null;
                }
            }
        }
        return $this->enterEndLocationDatetime ?? null;
    }

    public function getRoute(): array
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {

            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {

                $route = $this->getSharedMemory('route');
                if (!is_null($route)) {
                    $this->route = json_decode($route, true);
                }
            }
        }
        return $this->route;
    }

    public function setRoute(array $route): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this))) {
                $this->setSharedMemory('route', json_encode($route));
            }
        }
        $this->route = $route;
    }

    public function getEta(): int
    {
        return $this->eta;
    }

    public function setEta(int $eta): void
    {
        $this->eta = $eta;
    }

    public function setRouteDistance(float $distance): void
    {
        $this->routeDistance = $distance;
    }

    public function getRouteDistance(): float
    {
        return $this->routeDistance;
    }

    public function setEnterStoreRadius(string $enterStoreRadius): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this))) {
                $this->setSharedMemory('enter_store_radius', $enterStoreRadius);
            }
        }
        $this->enterStoreRadius = $enterStoreRadius;
    }

    public function getEnterStoreRadius(): string
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $enterStoreRadius = $this->getSharedMemory('enter_store_radius');
                if (!is_null($enterStoreRadius)) {
                    $this->enterStoreRadius = $enterStoreRadius;
                }
            }
        }
        return $this->enterStoreRadius;
    }

    public function setStoreRadius(int $storeRadius): void
    {
        $this->storeRadius = $storeRadius;
    }

    public function getStoreRadius(): int
    {
        return $this->storeRadius;
    }

    public function setKillOtherMovers(bool $killOtherMovers): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this))) {
                $this->setSharedMemory('kill_other_movers', $killOtherMovers);
            }
        }
        $this->killOtherMovers = $killOtherMovers;
    }

    public function getKillOtherMovers(): bool
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this))) {
                $killOtherMovers = $this->getSharedMemory('kill_other_movers');
                if (!is_null($killOtherMovers)) {
                    $this->killOtherMovers = $killOtherMovers;
                }
            }
        }
        return $this->killOtherMovers;
    }
}
