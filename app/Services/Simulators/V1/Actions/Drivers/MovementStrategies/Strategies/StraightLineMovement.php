<?php

namespace App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Strategies;

use App\Services\Simulators\V1\Actions\Drivers\MovementStrategies\Interfaces\MovementStrategy;
use App\Services\Simulators\V1\Entities\Drivers\Abstractions\Driver;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Moveable;

class StraightLineMovement implements MovementStrategy
{
    private Entity&Driver $driver;

    public function __construct(Entity&Moveable&Driver $driver)
    {
        $this->driver = $driver;
    }

    public function move()
    {
        error_log("PID - " . getmypid() . " - " . $this->driver->getDriverId() . " straight line movement strategy");

        $radius = config('services.simulator.v1.movement.straight_line.radius');

        $startLatitude = $this->driver->getStartLatitude();
        $startLongitude = $this->driver->getStartLongitude();

        $endLatitude = $this->driver->getEndLatitude();
        $endLongitude = $this->driver->getEndLongitude();

        error_log("PID - " . getmypid() . " - " . $this->driver->getDriverId() . " -  start location : " . json_encode(['latitude' => $startLatitude, 'longitude' => $startLongitude]) . " - end location : " . json_encode(['latitude' => $endLatitude, 'longitude' => $endLongitude]));

        if ($startLatitude == $endLatitude && $startLongitude == $endLongitude) {
            return;
        }

        // in kilometers
        if ($startLatitude == 0.0 || $startLongitude == 0.0) {
            $coordinates = $this->getCoordinatesInRadius($endLatitude, $endLongitude, $radius);

            $location = $this->generateRandomLocationInsideZone($coordinates);
        } else {
            // Calculate the bearing (direction) from the driver to the pickup location
            $bearing = $this->calculateBearing($startLatitude, $startLongitude, $endLatitude, $endLongitude);
            error_log("PID - " . getmypid() . " - " . $this->driver->getDriverId() . " -  bearing : " . $bearing);
            // Calculate a new location 100 meters away in the direction of the pickup location
            $location = $this->calculateDestination($startLatitude, $startLongitude, $bearing, config('services.simulator.v1.movement.straight_line.step'), $endLatitude, $endLongitude);
        }
        $this->driver->setLatitude($location['latitude']);
        $this->driver->setLongitude($location['longitude']);

        $this->driver->setStartLatitude($location['latitude']);
        $this->driver->setStartLongitude($location['longitude']);
        error_log("PID - " . getmypid() . " - " . $this->driver->getDriverId() . " -  new location : " . json_encode($location));
    }

    function generateRandomLocationInsideZone(array $boundaryCoordinates)
    {
        // Generate random latitude and longitude within the boundary coordinates
        $minLat = null;
        $maxLat = null;
        $minLng = null;
        $maxLng = null;

        foreach ($boundaryCoordinates as $coordinate) {
            $latitude = $coordinate['latitude'];
            $longitude = $coordinate['longitude'];

            if ($minLat === null || $latitude < $minLat) {
                $minLat = $latitude;
            }

            if ($maxLat === null || $latitude > $maxLat) {
                $maxLat = $latitude;
            }

            if ($minLng === null || $longitude < $minLng) {
                $minLng = $longitude;
            }

            if ($maxLng === null || $longitude > $maxLng) {
                $maxLng = $longitude;
            }
        }

        // Generate random latitude and longitude within the bounding box
        $randomLatitude = mt_rand($minLat * 1000000, $maxLat * 1000000) / 1000000;
        $randomLongitude = mt_rand($minLng * 1000000, $maxLng * 1000000) / 1000000;
        // Check if the random point is inside the polygon
        while (!$this->isPointInPolygon(['latitude' => $randomLatitude, 'longitude' => $randomLongitude], $boundaryCoordinates)) {
            $randomLatitude = mt_rand($minLat * 1000000, $maxLat * 1000000) / 1000000;
            $randomLongitude = mt_rand($minLng * 1000000, $maxLng * 1000000) / 1000000;
        }

        return ['latitude' => $randomLatitude, 'longitude' => $randomLongitude];
    }

    function isPointInPolygon($point, $polygon)
    {
        error_log("checking if point is in polygon");
        $x = $point['latitude'];
        $y = $point['longitude'];

        $inside = false;
        $n = count($polygon);
        $j = $n - 1;

        for ($i = 0; $i < $n; $i++) {
            $xi = $polygon[$i]['latitude'];
            $yi = $polygon[$i]['longitude'];
            $xj = $polygon[$j]['latitude'];
            $yj = $polygon[$j]['longitude'];

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }

    function getCoordinatesInRadius($latitude, $longitude, $radius)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $delta = $radius / $earthRadius;
        $lat1 = deg2rad($latitude);
        $lon1 = deg2rad($longitude);

        $points = array();

        for ($i = 0; $i < 360; $i += 10) { // You can adjust the step (10 degrees in this case)
            $angle = deg2rad($i);
            $lat2 = asin(sin($lat1) * cos($delta) + cos($lat1) * sin($delta) * cos($angle));
            $lon2 = $lon1 + atan2(sin($angle) * sin($delta) * cos($lat1), cos($delta) - sin($lat1) * sin($lat2));
            $lat2 = rad2deg($lat2);
            $lon2 = rad2deg($lon2);

            $points[] = ['latitude' => $lat2, 'longitude' => $lon2];
        }

        return $points;
    }

    function calculateBearing($lat1, $lon1, $lat2, $lon2)
    {
        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Calculate the bearing (direction) in radians
        $deltaLon = $lon2 - $lon1;
        $y = sin($deltaLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($deltaLon);
        $bearing = atan2($y, $x);

        // Convert bearing to degrees
        $bearing = rad2deg($bearing);

        // Ensure the bearing is within the range [0, 360) degrees
        $bearing = ($bearing + 360) % 360;

        return $bearing;
    }


    function calculateDestination($startLatitude, $startLongitude, $bearing, $distance, $stopLatitude, $stopLongitude)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $startLatitude = deg2rad($startLatitude);
        $startLongitude = deg2rad($startLongitude);


        // Calculate the destination based on the specified distance and bearing
        $lat2 = asin(sin($startLatitude) * cos($distance / $earthRadius) + cos($startLatitude) * sin($distance / $earthRadius) * cos(deg2rad($bearing)));
        $lon2 = $startLongitude + atan2(sin(deg2rad($bearing)) * sin($distance / $earthRadius) * cos($startLatitude), cos($distance / $earthRadius) - sin($startLatitude) * sin($lat2));

        // Convert back to degrees
        $lat2 = rad2deg($lat2);
        $lon2 = rad2deg($lon2);

        // Check if the calculated point is closer to the stop location than the specified distance
        $distanceToStop = $this->calculateDistance($lat2, $lon2, $stopLatitude, $stopLongitude);

        if ($distanceToStop < $distance) {
            return ['latitude' => $stopLatitude, 'longitude' => $stopLongitude];
        }

        return ['latitude' => $lat2, 'longitude' => $lon2];
    }

    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }


}
