<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Get current weather for a city.
     *
     * @param  string  $city  City name (default: London)
     * @return array|null Weather data or null on error
     */
    public function getCurrentWeather(string $city = 'London'): ?array
    {
        $apiKey = config('services.openweather.api_key');

        if (! $apiKey) {
            Log::warning('OpenWeather API key not configured');

            return null;
        }

        // Cache weather data for 10 minutes
        return Cache::remember("weather.{$city}", 600, function () use ($city, $apiKey) {
            try {
                $response = Http::timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric', // Celsius
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    return [
                        'city' => $data['name'],
                        'country' => $data['sys']['country'] ?? '',
                        'temperature' => round($data['main']['temp']),
                        'feels_like' => round($data['main']['feels_like']),
                        'description' => ucfirst($data['weather'][0]['description']),
                        'icon' => $data['weather'][0]['icon'],
                        'humidity' => $data['main']['humidity'],
                        'wind_speed' => round($data['wind']['speed'] ?? 0, 1),
                    ];
                }

                Log::warning('Weather API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('Weather API error', ['message' => $e->getMessage()]);

                return null;
            }
        });
    }

    /**
     * Get weather icon URL.
     */
    public function getIconUrl(string $icon): string
    {
        return "https://openweathermap.org/img/wn/{$icon}@2x.png";
    }
}
