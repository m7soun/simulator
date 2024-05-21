<?php

namespace App\Services\Requests\V1\Templates\Drivers\Geo;

use App\Services\Requests\V1\Templates\Abstractions\Templatable;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;

class Geo extends Templatable
{
    public $endpoint = '/geo/geoAPI.php';
    public $method = 'POST';

    public $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    public $parameters = [];

    public $query = [];

    public function __construct(Driver $driver)
    {
        $this->setEndpoint(config('services.requests.v1.api.defaults.geo_base_url') . $this->endpoint);
        $this->setMethod($this->method);
        $this->setHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $driver->getAccessToken(),
        ]);

        $positions = [
            [
                "lat" => $driver->getLatitude(),
                "long" => $driver->getLongitude(),
                "speed" => rand(0, 100),
                "time" => time(),
                "b_value" => 0,
                "b_char" => 0,
            ],
        ];

        $this->setQuery([
            'car_id' => $driver->getDriverId(),
            'client_id' => $driver->getClientId(),
            'driver_name' => $driver->getDriverName(),
            'driver_status' => $driver->getStatus(),
            'imsi' => $driver->getImsi(),
            'positions' => $positions,
        ]);

        $this->setParameters([
            'car_id' => $driver->getDriverId(),
            'client_id' => $driver->getClientId(),
            'driver_name' => $driver->getDriverName(),
            'driver_status' => $driver->getStatus(),
            'imsi' => $driver->getImsi(),
            'positions' => $positions,
            'store_radius_enter_timestamp' => $driver->getEnterStoreRadius()
        ]);
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setQuery(array $query): void
    {
        $this->query = $query;
    }
}
