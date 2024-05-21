<?php

namespace App\Services\Simulators\V1\Entities\Interfaces\Drivers;

interface Driver
{
    public function __construct(array $driverDetails);

    public function initDriver(): void;

    public function setDriverDetails(array $driverDetails): void;

    public function setDriverId(int $driverId): void;

    public function getDriverId(): int;

    public function setLoginUsername(string $loginUsername): void;

    public function getLoginUsername(): string;

    public function setLoginPassword(string $loginPassword): void;

    public function getLoginPassword(): string;

    public function getEntityName(): string;

    public function setEntityName(string $entityName): void;

    public function setImsi(string $imsi): void;

    public function getImsi(): string;

    public function setEnterStoreRadius(string $enterStoreRadius): void;

    public function getEnterStoreRadius(): string;

    public function setStoreRadius(int $storeRadius): void;

    public function getStoreRadius(): int;
}
