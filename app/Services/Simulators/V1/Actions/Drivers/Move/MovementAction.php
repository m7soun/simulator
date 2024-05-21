<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Move;


use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Geo\Geo;
use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Movement;
use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Strategies\RouteBasedMovement;
use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Strategies\StraightLineMovement;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Moveable;
use App\Services\Threads\V1\Threadables\Drivers\DriverMovement;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class MovementAction implements Action
{
    protected Driver&Entity $entity;

    protected $thread;

    public function __construct(Driver&Entity&Moveable $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {

        $this->getEntity()->setIsMoving(true);

        error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  Start moving");
        $driverStatus = $this->getEntity()->getStatus();
        while (true) {

            if ($driverStatus !== $this->getEntity()->getStatus()) {
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " - driver status changed from " . $driverStatus . " to " . $this->getEntity()->getStatus());
                $this->getEntity()->setIsMoving(false);
                exit(0);
            }

            $movement = new Movement();

            if ($driverStatus === Driver::STATUS_AVAILABLE || $driverStatus === Driver::STATUS_RETURNING) {
                if (count($this->getEntity()->getRoute())) {
                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  RouteBasedMovement movement , driver have route and he is available");
                    $movement->setMovementStartegy(new RouteBasedMovement($this->entity));
                } else {
                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  StraightLineMovement movement , driver dont have route and he is available");
                    $movement->setMovementStartegy(new StraightLineMovement($this->entity));
                }
            } elseif ($driverStatus === Driver::STATUS_ASSIGNED) {
                if (count($this->getEntity()->getRoute())) {
                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  RouteBasedMovement movement , driver have route and he is assigned");
                    $movement->setMovementStartegy(new RouteBasedMovement($this->entity));
                } else {
                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  StraightLineMovement movement , driver dont have route and he is assigned");
                    $movement->setMovementStartegy(new StraightLineMovement($this->entity));
                }
            } elseif ($driverStatus === Driver::STATUS_IN_PROGRESS) {
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  RouteBasedMovement movement , driver have route and he is in progress");
                $movement->setMovementStartegy(new RouteBasedMovement($this->entity));
            } else {
                continue;
            }

            $movement->move();

            error_log('PID : ' . getmypid() . ' - Driver : ' . $this->getEntity()->getDriverId() . ' - driver current location is ' . $this->getEntity()->getLatitude() . ' , ' . $this->getEntity()->getLongitude() . ' and driver destination is ' . $this->getEntity()->getEndLatitude() . ' , ' . $this->getEntity()->getEndLongitude());

            if ($this->getEntity()->getOrder()) {
                $driverLat = $this->getEntity()->getLatitude();
                $driverLng = $this->getEntity()->getLongitude();

                if ($driverStatus === Driver::STATUS_AVAILABLE || $driverStatus === Driver::STATUS_ASSIGNED || $driverStatus === Driver::STATUS_RETURNING) {
                    $stopLocationLat = $this->getEntity()->getOrder()->getPoi()->getLocation()->getLatitude();
                    $stopLocationLong = $this->getEntity()->getOrder()->getPoi()->getLocation()->getLongitude();
                } elseif ($driverStatus === Driver::STATUS_IN_PROGRESS) {
                    $stopLocationLat = $this->getEntity()->getOrder()->getCustomer()->getLocation()->getLatitude();
                    $stopLocationLong = $this->getEntity()->getOrder()->getCustomer()->getLocation()->getLongitude();
                }

                $this->getEntity()->setDistance($this->haversineDistance($driverLat, $driverLng, $stopLocationLat, $stopLocationLong));
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  distance between driver and stop location is " . $this->getEntity()->getDistance() . " meters");

                if ($this->getEntity()->getDistance() <= config('services.simulator.v1.movement.defaults.arrival_distance')) {
                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  driver arrived to stop location");

                    if (is_null($this->getEntity()->getEnterEndLocationDatetime()) || empty($this->getEntity()->getEnterEndLocationDatetime())) {
                        $this->getEntity()->setEnterEndLocationDatetime(new \DateTime());
                    }
                } else {
                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  driver is not in stop location or he is already out of stop location");

                    $this->getEntity()->setEnterEndLocationDatetime(null);
                }
            }

            if ($this->getEntity()->getDistance() <= $this->getEntity()->getStoreRadius()) {
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  driver entered store radius");

                if (is_null($this->getEntity()->getEnterStoreRadius()) || empty($this->getEntity()->getEnterStoreRadius())) {

                    error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  driver entered store radius for the first time : " . (new \DateTime())->format('Y-m-d H:i:s'));

                    $this->getEntity()->setEnterStoreRadius((new \DateTime())->format('Y-m-d H:i:s'));
                }
            } else {
                error_log("PID: " . getmypid() . " Driver: " . $this->getEntity()->getDriverId() . " -  driver left store radius or he is already out of store radius");
                $this->getEntity()->setEnterEndLocationDatetime('');
            }

            Client::create(new Geo($this->entity))->call();

            sleep(config('services.simulator.v1.movement.defaults.sleep'));
        }
    }

    public function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000;

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    public function addPrerequisite(Action $action): void
    {
        $this->prerequisites[] = $action;
    }

    public function hasPrerequisitesMet(): bool
    {
        return true;
    }

    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function isThreadable(): bool
    {
        return true;
    }

    public function getThreadable(): Threadable|null
    {
        return new DriverMovement();
    }

    public function setThread($thead): void
    {
        $this->thread = $thead;
    }
}
