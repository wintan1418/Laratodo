# Laravel API Integration Guide

## Integrating External APIs in Laravel

This guide shows you how we integrated the **OpenWeatherMap API** into our Task application.

---

## Why Weather API for a Todo App?

**Practical Use Cases:**
- Show weather on dashboard (planning outdoor tasks)
- Suggest tasks based on weather (e.g., "Nice day for outdoor tasks!")
- Help users plan their day better

**Other API Ideas:**
- **Quotes API** - Motivational quotes
- **Calendar API** - Google Calendar integration
- **Time API** - Timezone information
- **News API** - Daily news headlines
- **Currency API** - For expense tracking tasks

---

## Laravel's HTTP Client

Laravel has a **built-in HTTP client** - no external packages needed!

```php
use Illuminate\Support\Facades\Http;

$response = Http::get('https://api.example.com/data');
$data = $response->json();
```

**Features:**
- Simple API
- Automatic JSON parsing
- Timeout handling
- Error handling
- Request/response logging

---

## Our Weather API Integration

### Step 1: Create a Service Class

**Why Services?**
- Keeps business logic separate from controllers
- Reusable across the application
- Easy to test
- Follows Single Responsibility Principle

**File:** `app/Services/WeatherService.php`

```php
class WeatherService
{
    public function getCurrentWeather(string $city = 'London'): ?array
    {
        // Uses Laravel's HTTP client
        $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
            'q' => $city,
            'appid' => config('services.openweather.api_key'),
            'units' => 'metric',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
```

### Step 2: Configuration

**File:** `config/services.php`

```php
'openweather' => [
    'api_key' => env('OPENWEATHER_API_KEY'),
],
```

**File:** `.env`

```env
OPENWEATHER_API_KEY=your_api_key_here
```

### Step 3: Caching

**Why Cache?**
- Reduces API calls (saves money!)
- Faster response times
- Better user experience

```php
return Cache::remember("weather.{$city}", 600, function () {
    // API call here
    // Cache for 10 minutes (600 seconds)
});
```

### Step 4: Error Handling

```php
try {
    $response = Http::timeout(5)->get(...);
    
    if ($response->successful()) {
        return $response->json();
    }
    
    Log::warning('Weather API failed', ['status' => $response->status()]);
    return null;
} catch (\Exception $e) {
    Log::error('Weather API error', ['message' => $e->getMessage()]);
    return null;
}
```

### Step 5: Use in Controller

```php
class TaskController extends Controller
{
    public function __construct(
        private WeatherService $weatherService
    ) {}

    public function index(): View
    {
        $tasks = Auth::user()->tasks()->latest()->paginate(10);
        $weather = $this->weatherService->getCurrentWeather();
        
        return view('tasks.index', compact('tasks', 'weather'));
    }
}
```

### Step 6: Display in View

```blade
@if($weather)
    <div class="weather-widget">
        <h3>{{ $weather['city'] }}</h3>
        <p>{{ $weather['temperature'] }}°C</p>
        <p>{{ $weather['description'] }}</p>
    </div>
@endif
```

---

## Laravel HTTP Client Methods

### GET Request
```php
$response = Http::get('https://api.example.com/users');
```

### POST Request
```php
$response = Http::post('https://api.example.com/users', [
    'name' => 'John',
    'email' => 'john@example.com',
]);
```

### With Headers
```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer token',
    'Accept' => 'application/json',
])->get('https://api.example.com/data');
```

### With Authentication
```php
$response = Http::withToken($token)->get('https://api.example.com/data');
```

### Timeout
```php
$response = Http::timeout(10)->get('https://api.example.com/data');
```

### Retry on Failure
```php
$response = Http::retry(3, 100)->get('https://api.example.com/data');
```

### Check Response
```php
if ($response->successful()) {
    $data = $response->json();
}

if ($response->failed()) {
    // Handle error
}

$status = $response->status(); // 200, 404, 500, etc.
```

---

## Getting an OpenWeatherMap API Key

1. **Visit:** https://openweathermap.org/api
2. **Sign up** for a free account
3. **Get API key** from dashboard
4. **Add to `.env`:**
   ```env
   OPENWEATHER_API_KEY=your_api_key_here
   ```
5. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

**Free Tier Limits:**
- 60 calls/minute
- 1,000,000 calls/month
- Perfect for learning!

---

## Best Practices

### 1. Use Service Classes
```php
// ✅ Good - Separated logic
class WeatherService { }

// ❌ Bad - Logic in controller
class TaskController {
    public function index() {
        $response = Http::get(...); // Don't do this!
    }
}
```

### 2. Cache API Responses
```php
// ✅ Good - Cached
Cache::remember('weather', 600, fn() => Http::get(...));

// ❌ Bad - Every request hits API
Http::get(...);
```

### 3. Handle Errors Gracefully
```php
// ✅ Good - Handles errors
try {
    $weather = $this->weatherService->getCurrentWeather();
} catch (\Exception $e) {
    $weather = null; // App still works!
}
```

### 4. Use Configuration Files
```php
// ✅ Good - Config file
config('services.openweather.api_key')

// ❌ Bad - Direct env()
env('OPENWEATHER_API_KEY') // Only in config files!
```

### 5. Log API Calls
```php
Log::info('Weather API called', ['city' => $city]);
Log::warning('Weather API failed', ['status' => $status]);
```

---

## Testing API Integrations

### Mock HTTP Responses
```php
Http::fake([
    'api.openweathermap.org/*' => Http::response([
        'name' => 'London',
        'main' => ['temp' => 15],
    ], 200),
]);

$weather = $this->weatherService->getCurrentWeather();
$this->assertEquals('London', $weather['city']);
```

---

## Common API Patterns

### 1. RESTful APIs
```php
// GET /api/users
Http::get('https://api.example.com/users');

// POST /api/users
Http::post('https://api.example.com/users', $data);

// PUT /api/users/1
Http::put('https://api.example.com/users/1', $data);

// DELETE /api/users/1
Http::delete('https://api.example.com/users/1');
```

### 2. Query Parameters
```php
Http::get('https://api.example.com/search', [
    'q' => 'laravel',
    'page' => 1,
    'limit' => 10,
]);
```

### 3. JSON Requests
```php
Http::asJson()->post('https://api.example.com/data', [
    'name' => 'John',
]);
```

---

## Our Implementation Files

**Service:**
- `app/Services/WeatherService.php` - Handles API calls

**Controller:**
- `app/Http/Controllers/WeatherController.php` - API endpoint

**Configuration:**
- `config/services.php` - API key configuration

**View:**
- `resources/views/tasks/index.blade.php` - Weather display

**Routes:**
- `routes/web.php` - Weather route

---

## Summary

**Key Concepts:**
- ✅ Laravel has built-in HTTP client (no packages needed!)
- ✅ Use **Service classes** for API logic
- ✅ **Cache** API responses
- ✅ **Handle errors** gracefully
- ✅ Use **configuration files** for API keys
- ✅ **Log** API calls for debugging

**Our Weather Integration:**
- Shows weather on tasks page
- Cached for 10 minutes
- Handles errors gracefully
- Uses Laravel's HTTP client

**Next Steps:**
1. Get OpenWeatherMap API key
2. Add to `.env` file
3. Refresh page to see weather!

