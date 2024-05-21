<?php

namespace App\Services\Executors\V1\Interfaces;

interface Invoker
{
    public static function invoke(Command $command);

    public static function invokeWith(Command $command, array $args);

}
