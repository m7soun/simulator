<?php

namespace App\Console\Commands\V1\Drivers;

use App\Services\Drivers\V1\DriversService;
use App\Services\Loggers\V1\Facades\Logging as Logger;
use App\Services\Requests\V1\Clients\Guzzles\Client;
use App\Services\Teams\V1\TeamsService;
use App\Services\Threads\V1\Adapters\Adaptee\PcntlThread;
use App\Services\Threads\V1\Threadables\Drivers\BasicDriver;
use Illuminate\Console\Command;

class OrderSimulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's:o';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate drivers';


    public function __construct(private Logger $logger)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->logger->info('Order Simulator: Started');
        $polygon = [

            [
                54.3634672,
                24.498291
            ],
            [
                54.3629265,
                24.4973339
            ],
            [
                54.3559742,
                24.4917118
            ],
            [
                54.3512535,
                24.4874169
            ],
            [
                54.3405247,
                24.4730898
            ],
            [
                54.3212128,
                24.4602029
            ],
            [
                54.3315125,
                24.4576254
            ],
            [
                54.3509102,
                24.4535637
            ],
            [
                54.3498802,
                24.452314
            ],
            [
                54.3537855,
                24.4470731
            ],
            [
                54.3655958,
                24.4353564
            ],
            [
                54.3713894,
                24.430708
            ],
            [
                54.3756809,
                24.4345361
            ],
            [
                54.3806591,
                24.431333
            ],
            [
                54.3976879,
                24.4425199
            ],
            [
                54.3926668,
                24.4480661
            ],
            [
                54.3831739,
                24.4578143
            ],
            [
                54.3875513,
                24.4612901
            ],
            [
                54.3956194,
                24.4677336
            ],
            [
                54.392787,
                24.4724586
            ],
            [
                54.3874912,
                24.4810882
            ],
            [
                54.3818264,
                24.4871013
            ],
            [
                54.3855686,
                24.4901311
            ],
            [
                54.3793888,
                24.497354
            ],
            [
                54.3770714,
                24.4997355
            ],
            [
                54.373209,
                24.5033272
            ],
            [
                54.3698187,
                24.5045374
            ],
            [
                54.3634672,
                24.498291
            ]
        ];


        while (true) {
            $orderData = $this->generateOrderData($polygon);

            // Send the order data to the API
            $this->sendOrderData($orderData);

            error_log('Order Simulator: Order sent: ' . $orderData['order_number']);

            sleep(2);
        }

    }

    private function generateOrderData()
    {
        $endCoordinates = [
            ['latitude' => 24.47914, 'longitude' => 54.3703], // Existing coordinate
            ['latitude' => 24.4870888, 'longitude' => 54.3654433], // Additional point
            ['latitude' => 24.4783928, 'longitude' => 54.3816149], // Additional point
            ['latitude' => 24.4683622, 'longitude' => 54.3553876], // Additional point
            ['latitude' => 24.4506448, 'longitude' => 54.3725366], // Additional point
            ['latitude' => 24.4838127, 'longitude' => 54.4120163], // Additional point
            ['latitude' => 24.5045255, 'longitude' => 54.391261], // Additional point
            ['latitude' => 24.4482024, 'longitude' => 54.3928636], // Additional point
            ['latitude' => 24.4358672, 'longitude' => 54.3766866], // Additional point
            ['latitude' => 24.4332777, 'longitude' => 54.4134107], // Additional point
            ['latitude' => 24.4336293, 'longitude' => 54.4352975], // Additional point
            ['latitude' => 24.4269088, 'longitude' => 54.4276157], // Additional point
            ['latitude' => 24.4643306, 'longitude' => 54.336569], // Additional point
            ['latitude' => 24.4589008, 'longitude' => 54.3715021], // Additional point
            ['latitude' => 24.462065, 'longitude' => 54.3893119], // Additional point
            ['latitude' => 24.4531583, 'longitude' => 54.3961355], // Additional point
            ['latitude' => 24.4900389, 'longitude' => 54.3790866], // Additional point
        ];

        $radius = 1; // 1 km
        error_log("generating order data");
        $uniqueOrderNumber = uniqid("Order_Simulation_", true);

        $randomCoordinate = $endCoordinates[rand(0, count($endCoordinates) - 1)];
        var_dump($randomCoordinate);

        $orderData = [
            'order_number' => $uniqueOrderNumber,
            'pickup_number' => '7312023',
            'amount' => rand(10, 50) + rand(0, 99) / 100, // Random amount
            'payment_method' => 'COD',
            'pos_number' => 78596,
            'destination_name' => 'Al Adil Trading Co.',
            'destination_address' => uniqid("Order_Simulation_", true),
            'destination_lat' => $randomCoordinate['latitude'],
            'destination_ln' => $randomCoordinate['longitude'],
            'recipient' => 'test customer',
            'recipient_phone' => '0522607738',
            'vehicle_type' => 'BIKE',
            'order_source_name' => 'Local Script',
            'extra_info' => 'Simulation Test',
        ];

        return $orderData;
    }

    private function getRandomCoordinateInRadius(array $coordinates, float $radius)
    {
        // Randomly select one of the coordinates
        $randomCoordinate = $coordinates[array_rand($coordinates)];

        // You can adjust the coordinates here within the specified radius
        // For example, you could add/subtract small random values to simulate slight variations

        return $randomCoordinate;
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

        // Seed the random number generator to get different results in each round
        mt_srand();

        // Generate random latitude and longitude within the bounding box
        $randomLatitude = mt_rand($minLat * 1000000, $maxLat * 1000000) / 1000000;
        $randomLongitude = mt_rand($minLng * 1000000, $maxLng * 1000000) / 1000000;
        // Check if the random point is inside the polygon
        while (!$this->isPointInPolygon(['latitude' => $randomLatitude, 'longitude' => $randomLongitude], $boundaryCoordinates)) {
            // Regenerate random coordinates
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


    private function sendOrderData(array $orderData)
    {
        // You can send the order data to the API using the Client class.
        // Create the Client instance, set the endpoint, headers, method, and body,
        // and then make the API request.
        Client::create()
            ->withEndpoint('https://order-service.test.lyve.global/v1/createPackage')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'text/plain',
            ])
            ->withMethod('POST')
            ->withBody([
                'hash' => 'ed3798d5b7291f3f79a74a6e8e06f856',
                'data' => [$orderData],
            ])
            ->call();
    }

}
