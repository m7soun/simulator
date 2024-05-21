<?php

namespace App\Services\Requests\V1\Templates\Drivers\Authentication;

use App\Services\Requests\V1\Templates\Abstractions\Templatable;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;

class ShiftOn extends Templatable
{
    public $endpoint = '/mobileAPI.php';

    public $method = '';

    public $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

    public $parameters = [
    ];

    public $query = [];

    public function __construct(Driver $driver)
    {

        $this->setEndpoint(config('services.requests.v1.api.defaults.mobile_proxy_base_url') . $this->getEndpoint());
        $this->setMethod('GET');
        $this->setHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $driver->getAccessToken()
        ]);


        $this->setQuery([
            'action' => 'shift',
            'imsi' => $driver->getImsi(),
            'operation' => 1
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
