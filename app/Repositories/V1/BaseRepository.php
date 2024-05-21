<?php

namespace App\Repositories\V1;

abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function setModel();

    abstract public function run(?array $data = []);
}
