<?php

namespace App\Services\Requests\V1\Templates\Abstractions;

abstract class Templatable
{
    const METHODS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    public $endpoint = '';

    abstract public function setEndpoint(string $endpoint);

    abstract public function setMethod(string $method);

    abstract public function setHeaders(array $headers);

    abstract public function setParameters(array $parameters);

    abstract public function getEndpoint(): string;

    abstract public function getMethod(): string;

    abstract public function getHeaders(): array;

    abstract public function getParameters(): array;

    abstract public function getQuery(): array;

    abstract public function setQuery(array $query): void;

    public function getRetryCount(): int
    {
        return 3;
    }

    public function getRetryDelay(): int
    {
        return 1;
    }


}
