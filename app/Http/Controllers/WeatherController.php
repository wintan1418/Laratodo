<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(
        private WeatherService $weatherService
    ) {}

    /**
     * Get current weather.
     */
    public function index(Request $request): JsonResponse
    {
        $city = $request->get('city', 'London');
        $weather = $this->weatherService->getCurrentWeather($city);

        if (! $weather) {
            return response()->json([
                'error' => 'Unable to fetch weather data',
            ], 500);
        }

        return response()->json($weather);
    }
}
