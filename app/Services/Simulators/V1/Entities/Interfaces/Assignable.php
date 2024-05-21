<?php

namespace App\Services\Simulators\V1\Entities\Interfaces;

use App\Services\Simulators\V1\Entities\Interfaces\Orders\Order;

interface Assignable
{
    public function setOrder(Order|int|null $order): void;

    public function setOrderId(int|null $order): void;

    public function getOrderId(): int|null;

    public function getOrder(): Order|null;
}
