<?php

namespace App\Services\Simulators\V1\Entities\Orders\BasicOrders;

use App\Services\Simulators\V1\Entities\Interfaces\Customerable;
use App\Services\Simulators\V1\Entities\Interfaces\Customers\Customer;
use App\Services\Simulators\V1\Entities\Interfaces\Orders\Order;
use App\Services\Simulators\V1\Entities\Interfaces\Poiable;
use App\Services\Simulators\V1\Entities\Interfaces\Pois\Poi;
use App\Services\Simulators\V1\Entities\Traits\SharedMemory;

class BasicOrder implements Order, Poiable, Customerable
{
    use SharedMemory;

    protected int $orderId;

    protected Poi $poi;

    protected Customer $customer;

    protected string $country;

    protected bool $isPickedUp;
    protected string $filePath = 'SharedMemory/Orders';


    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setPoi(Poi $poi): void
    {
        $this->poi = $poi;
    }

    public function getPoi(): Poi
    {
        return $this->poi;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setPickedUp(bool $pickedUp): void
    {
        $this->ispickedUp = $pickedUp;
    }

    public function getPickedUp(): bool
    {
        return $this->isPickedUp ?? false;
    }

    public function getFilePath(): string
    {
        return $this->filePath . '/' . $this->getOrderId() . '.json';
    }
}
