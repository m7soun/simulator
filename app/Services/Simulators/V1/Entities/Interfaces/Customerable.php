<?php

namespace App\Services\Simulators\V1\Entities\Interfaces;

use App\Services\Simulators\V1\Entities\Interfaces\Customers\Customer;

interface Customerable
{
    public function setCustomer(Customer $customer): void;

    public function getCustomer(): Customer;

}
