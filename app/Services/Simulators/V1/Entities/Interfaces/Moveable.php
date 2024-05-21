<?php

namespace App\Services\Simulators\V1\Entities\Interfaces;
interface Moveable
{
    public function setStartLatitude(float $latitude): void;

    public function getStartLatitude(): float;

    public function setStartLongitude(float $longitude): void;

    public function getStartLongitude(): float;

    public function setEndLatitude(float $latitude): void;

    public function getEndLatitude(): float;

    public function setEndLongitude(float $longitude): void;

    public function getEndLongitude(): float;

    public function setDistance(float $distance): void;

    public function getDistance(): float;

    public function setEnterEndLocationDatetime($date): void;

    public function getEnterEndLocationDatetime();

    public function getRoute(): array;

    public function setRoute(array $route): void;

    public function getEta(): int;

    public function setEta(int $eta): void;

    public function setRouteDistance(float $distance): void;

    public function getRouteDistance(): float;

    public function setKillOtherMovers(bool $kill): void;

    public function getKillOtherMovers(): bool;
}
