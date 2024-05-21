<?php

namespace App\Services\Simulators\V1\Entities\Customers\BasicCustomers;

use App\Services\Simulators\V1\Entities\Interfaces\Customers\Customer;
use App\Services\Simulators\V1\Entities\Interfaces\Locationable;
use App\Services\Simulators\V1\Entities\Interfaces\Locations\Location;

class BasicCustomer implements Customer, Locationable
{

    protected Location $location;

    protected int $customerId;

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

}
