<?php

namespace App\Services\Executors\V1\Commands\Drivers;

use App\Services\Executors\V1\Interfaces\Command;
use App\Services\Executors\V1\Interfaces\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver as DriverEntity;

class PrepareRoute implements Command, Driver
{
    protected DriverEntity $driver;

    public function __construct(DriverEntity $driver)
    {
        $this->setDriver($driver);
    }

    public function execute()
    {
        error_log("PID - " . getmypid() . " - driver " . $this->driver->getDriverId() . " preparing route");
        $totalDistance = $this->driver->getRouteDistance(); // Total distance
        $dynamicETA = $this->driver->getEta(); // Dynamic ETA in seconds
        $timeInterval = config('services.simulator.v1.movement.defaults.sleep'); // Time interval in seconds

        $speed = $totalDistance / $dynamicETA; // Calculate speed
        $pointsNeeded = ceil($totalDistance / ($speed * $timeInterval)); // Calculate the number of points needed

        $baseRoute = $this->driver->getRoute(); // Get the base route

        $distanceRate = $totalDistance / $pointsNeeded; // Calculate the distance rate

        $adjustedRoute = [$baseRoute[0]]; // Initialize with the first point
        $adjustedDistance = 0; // Initialize the adjusted distance

        for ($i = 1; $i < count($baseRoute) - 1; $i++) {
            $lat1 = $baseRoute[$i - 1]['lat'];
            $lng1 = $baseRoute[$i - 1]['lng'];
            $lat2 = $baseRoute[$i]['lat'];
            $lng2 = $baseRoute[$i]['lng'];

            // Calculate the distance between two consecutive points using a suitable formula
            $distance = $this->calculateDistance($lat1, $lng1, $lat2, $lng2);

            if ($distance > $distanceRate) {
                // Calculate how many new points to insert based on the distance
                $numNewPoints = floor($distance / $distanceRate);
                for ($j = 1; $j <= $numNewPoints; $j++) {
                    $newPoint = [
                        'lat' => $lat1 + ($lat2 - $lat1) * ($j / ($numNewPoints + 1)),
                        'lng' => $lng1 + ($lng2 - $lng1) * ($j / ($numNewPoints + 1)),
                    ];
                    $adjustedDistance += $distanceRate; // Increase adjusted distance
                    $newPoint['distance'] = $adjustedDistance; // Add the adjusted distance to the point
                    $adjustedRoute[] = $newPoint;
                }
            }

            $adjustedDistance += $distance; // Increase adjusted distance
            $baseRoute[$i]['distance'] = $adjustedDistance; // Add the adjusted distance to the original point
            $adjustedRoute[] = $baseRoute[$i];
        }

        $adjustedRoute[] = $baseRoute[count($baseRoute) - 1]; // Add the last point
        error_log("PID : " . getmypid() . " - driver " . $this->driver->getDriverId() . " route prepared");

        $this->driver->setRoute($adjustedRoute);
    }


    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Radius of the Earth in meters

        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        $latDiff = $lat2Rad - $lat1Rad;
        $lngDiff = $lng2Rad - $lng1Rad;

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($lngDiff / 2) * sin($lngDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;
        return $distance;
    }


    public function setDriver(DriverEntity $driver)
    {
        $this->driver = $driver;
    }
}
