<?php

namespace App\Services\Requests\V1\Templates\Drivers\Eta;

use App\Services\Requests\V1\Templates\Abstractions\Templatable;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Assignable;

class Eta extends Templatable
{
    //example
    public $endpoint = '/GetAll';

    public $method = '';

    public $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

    public $parameters = [
    ];

    public $query = [];

    public function __construct(Assignable $driver)
    {

        $this->setEndpoint(config('services.requests.v1.api.defaults.eta_base_url') . $this->getEndpoint());
        $this->setMethod('GET');
        $this->setHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $driver->getAccessToken()
        ]);
        $this->setQuery([
            'country' => $driver->getOrder()->getCountry(),
            'start_lon' => $driver->getLongitude(),
            'stop_lon' => $driver->getOrder()->getCustomer()->getLocation()->getLongitude(),
            'start_lat' => $driver->getLatitude(),
            'stop_lat' => $driver->getOrder()->getCustomer()->getLocation()->getLatitude(),
            'source' => 'mobile',
            'action' => 'GetAll',
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
        return 'POST';
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
