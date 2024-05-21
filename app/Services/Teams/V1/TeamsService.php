<?php

namespace App\Services\Teams\V1;

class TeamsService
{
    function __construct()
    {
    }

    public static function getEnabledTeamsForDriversSimulation()
    {
        error_log("PID - MASTER: " . getmypid() . " Getting enabled teams for drivers simulation");
        error_log("PID - MASTER: " . getmypid() . " Enabled teams for drivers simulation: " . config('services.simulator.v1.drivers.loop.teams'));
        return explodeCommaSeparatedString(config('services.simulator.v1.drivers.loop.teams'));
    }
}
