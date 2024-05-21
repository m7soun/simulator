<?php

namespace App\Services\Executors\V1\Invokers;


use App\Services\Executors\V1\Interfaces\Command;
use App\Services\Executors\V1\Interfaces\Invoker;

class FunctionInvoker implements Invoker
{
    public static function invoke(Command $command)
    {
        return $command->execute();
    }

    public static function invokeWith(Command $command, array $args)
    {
        return $command->execute(...$args);
    }
}
