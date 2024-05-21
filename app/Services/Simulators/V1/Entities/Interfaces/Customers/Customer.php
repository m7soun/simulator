<?php

namespace App\Services\Simulators\V1\Entities\Interfaces\Customers;

interface Customer
{

    public function setCustomerId(int $customerId): void;

    public function getCustomerId(): int;
}
