<?php

namespace App\Services\Requests\V1\Clients\Guzzles;

use App\Services\Requests\V1\Templates\Abstractions\Templatable;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class Client
{
    private $client;
    private $request;
    private $retryCount; // Number of retry attempts
    private $retryDelay; // Delay between retry attempts in seconds

    private function __construct()
    {
        $this->client = new GuzzleClient([
            'base_uri' => '',
            'timeout' => 10,
        ]);

        // Set default retry configuration
        $this->retryCount = 3; // Number of retry attempts
        $this->retryDelay = 1; // Delay between retry attempts in seconds
    }

    public static function create(Templatable|null $template = null)
    {
        if ($template) {
            return (new self())
                ->withEndpoint($template->getEndpoint())
                ->withHeaders($template->getHeaders())
                ->withBody($template->getParameters())
                ->withQuery($template->getQuery())
                ->withMethod($template->getMethod());
        }

        return new self();
    }

    public function withEndpoint($endpoint)
    {
        $this->request['endpoint'] = $endpoint;
        return $this;
    }

    public function withHeaders($headers)
    {
        $this->request['headers'] = $headers;
        return $this;
    }

    public function withBody($body)
    {
        if (count($body) === 0) {
            return $this;
        }
        $this->request['body'] = $body;
        return $this;
    }

    public function addBodyAttribute($key, $value)
    {
        if (!isset($this->request['body'])) {
            $this->request['body'] = [];
        }
        $this->request['body'][$key] = $value;
        return $this;
    }

    public function withQuery($queryParams)
    {
        $this->request['query_params'] = $queryParams;
        return $this;
    }

    public function addQuery($key, $value)
    {
        if (!isset($this->request['query_params'])) {
            $this->request['query_params'] = [];
        }
        $this->request['query_params'][$key] = $value;
        return $this;
    }

    public function withMethod($method)
    {
        $this->request['method'] = $method;
        return $this;
    }

    // Set the number of retry attempts
    public function setRetryCount($count)
    {
        $this->retryCount = $count;
        return $this;
    }

    // Set the delay between retry attempts in seconds
    public function setRetryDelay($delay)
    {
        $this->retryDelay = $delay;
        return $this;
    }

    public function call()
    {
        $method = $this->request['method'] ?? 'GET';

        for ($i = 0; $i < $this->retryCount; $i++) {
            try {
                $requestOptions = [
                    'headers' => $this->request['headers'],
                ];

                if (isset($this->request['query_params']) && !empty($this->request['query_params'])) {
                    $queryString = http_build_query($this->request['query_params']);
                    $this->request['endpoint'] .= '?' . $queryString;
                }

                if ($method === 'POST' && isset($this->request['body'])) {
                    $requestOptions['json'] = $this->request['body'];
                }
//                error_log("PID: " . getmypid() . " - " . $this->request['endpoint'] . " - " . json_encode($requestOptions));

                $response = $this->client->request($method, $this->request['endpoint'], $requestOptions);

//                error_log("PID: " . getmypid() . " - " . json_encode($response->getBody()));
                // Check for successful response status codes
                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    return json_decode($response->getBody(), true);
                }
            } catch (GuzzleException $e) {
                // Handle the exception or add a retry mechanism
                if ($i < $this->retryCount - 1) {
                    // Wait for the specified delay before retrying
                    sleep($this->retryDelay);
                } else {
                    // All retry attempts failed, throw an exception
                    throw new Exception('API request failed after retrying. ' . json_encode($this->request));
                }
            }
        }

        return null;
    }

    public function get()
    {
        return $this->call();
    }

    public function post()
    {
        return $this->withMethod('POST')->call();
    }

    public function put()
    {
        return $this->withMethod('PUT')->call();
    }

    public function delete()
    {
        return $this->withMethod('DELETE')->call();
    }

    public function patch()
    {
        return $this->withMethod('PATCH')->call();
    }
}
