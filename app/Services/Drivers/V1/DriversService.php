<?php

namespace App\Services\Drivers\V1;

use App\Repositories\V1\Drivers\GetDriversByTeamId;

class DriversService
{
    function __construct()
    {

    }

    public static function getDriversByTeamId(?array $teams = []): object
    {
        error_log("PID - MASTER : " . getmypid() . " - DriversService: getDriversByTeamId");

        return (new GetDriversByTeamId())->run($teams);
    }
}
