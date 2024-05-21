<?php

namespace App\Services\Simulators\V1\Entities\Interfaces\Orders;

interface Order
{
    public function setOrderId(int $orderId): void;

    public function getOrderId(): int;

    public function setCountry(string $country): void;

    public function getCountry(): string;

    public function setPickedUp(bool $pickedUp): void;

    public function getPickedUp(): bool;
}
