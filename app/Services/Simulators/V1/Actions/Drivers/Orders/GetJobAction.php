<?php

namespace App\Services\Simulators\V1\Actions\Drivers\Orders;

use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Requests\V1\Templates\Drivers\Orders\GetJob;
use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Customers\BasicCustomers\BasicCustomer;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Assignable;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Locations\BasicLocations\BasicLocation;
use App\Services\Simulators\V1\Entities\Pois\BasicPois\BasicPoi;
use App\Services\Threads\V1\Threadables\Interfaces\Threadable;

class GetJobAction implements Action
{
    protected Driver&Entity $entity;

    public function __construct(Driver&Entity&Assignable $driver)
    {
        $this->setEntity($driver);
    }

    public function execute($data = null): void
    {
        error_log("PID - " . getmypid() . " - " . $this->getEntity()->getDriverId() . " get driver orders action");
        $driverOrders = Client::create(new GetJob($this->getEntity()))->call();

        $pickupLocation = new BasicLocation();
        $pickupLocation->setLatitude($driverOrders['points'][0]['latitude']);
        $pickupLocation->setLongitude($driverOrders['points'][0]['longitude']);
        $pickupLocation->setAddress($driverOrders['points'][0]['address']);

        $poi = new BasicPoi();

        $poi->setLocation($pickupLocation);
        $poi->setPoiId($driverOrders['points'][0]['poi_id']);
        $poi->setRadius($driverOrders['points'][0]['radius']);


        $dropoffLocation = new BasicLocation();
        $dropoffLocation->setLatitude($driverOrders['points'][1]['latitude']);
        $dropoffLocation->setLongitude($driverOrders['points'][1]['longitude']);
        $dropoffLocation->setAddress($driverOrders['points'][1]['address']);

        $customer = new BasicCustomer();
        $customer->setLocation($dropoffLocation);
        $customer->setCustomerId($driverOrders['points'][1]['poi_id']);

        $this->getEntity()->getOrder()->setPoi($poi);
        $this->getEntity()->getOrder()->setCustomer($customer);
        $this->getEntity()->getOrder()->setCountry($driverOrders['country']);
        $this->getEntity()->setStatus(Driver::STATUS_ASSIGNED);
        sleep(5);
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
        return false;
    }

    public function getThreadable(): Threadable|null
    {
        return null;
    }
}
