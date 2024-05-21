<?php

namespace App\Services\Requests\V1\Templates\Drivers\Orders;

use App\Services\Requests\V1\Templates\Abstractions\Templatable;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;

class DropOff extends Templatable
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
            'action' => 'changePoiStatus',
            'imsi' => $driver->getImsi(),
            'jobid' => $driver->getOrder()->getOrderId(),
            'lng' => $driver->getLongitude(),
            'lat' => $driver->getLatitude(),
            'lon' => $driver->getLongitude(),
            'accuracy' => 9.25,
            'offline' => 0,
            'status_id' => 3,
            'gps_timestamp' => date('Y-m-d H:i:s'),
            'compliance_type' => 'Pin',
            'time' => date('Y-m-d H:i:s'),
            'poi_id' => $driver->getOrder()->getCustomer()->getCustomerId(),
            'time_delta' => 1727,
            'inprogress_today_orders_counter' => 0
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
