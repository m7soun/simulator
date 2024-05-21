<?php

namespace App\Services\Loggers\V1\Facades\Adapters\Adaptee;


use App\Services\Loggers\V1\Facades\Adapters\Interfaces\Connection;

class ConnectToDecorator implements Connection
{
    private $decorator = null;

    public function connect()
    {
        dd('am here');
    }

    public function setDecorator($decorator)
    {
        $this->decorator = $decorator;
    }

    public function getDecorator()
    {
        return $this->decorator;
    }

    public function error(string $message): void
    {
        $this->decorator->error($message);
    }

    public function warning(string $message): void
    {
        $this->decorator->warning($message);
    }

    public function info(string $message): void
    {
        $this->decorator->info($message);
    }

    public function debug(string $message): void
    {
        $this->decorator->debug($message);
    }
}
