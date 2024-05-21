<?php

namespace App\Http\Controllers\Api\_health;

use App\Http\Controllers\Controller;
use App\Services\Health\V1\HealthCheckService;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function __construct(private readonly HealthCheckService $healthCheckService)
    {
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $results = $this->healthCheckService->run();
        return response()->json($results);
    }
}
