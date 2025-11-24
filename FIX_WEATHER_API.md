# Fix Weather API - Step by Step Guide

## Current Status
✅ Interface is working (you can see the warning message)  
❌ API key is invalid or not activated

## Quick Fix Steps

### Step 1: Get a New API Key

1. **Visit:** https://openweathermap.org/api
2. **Click:** "Sign Up" (top right) if you don't have an account
3. **Fill in:**
   - Username
   - Email
   - Password
4. **Verify your email** (check inbox)
5. **After login, go to:** https://home.openweathermap.org/api_keys
6. **You'll see:** "Default" API key (or click "Generate" to create new one)
7. **Copy the key** (it's a long string like `abc123def456...`)

### Step 2: Update Your .env File

**Option A: Edit manually**
```bash
# Open .env file
nano .env
# or
code .env
```

Find this line:
```
OPENWEATHER_API_KEY=67a3f1728c6a36171b6996f53630c8d5
```

Replace with your new key:
```
OPENWEATHER_API_KEY=your_new_key_here
```

**Option B: Use command line**
```bash
# Replace YOUR_NEW_KEY with your actual key
sed -i 's/OPENWEATHER_API_KEY=.*/OPENWEATHER_API_KEY=YOUR_NEW_KEY/' .env
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test Your Key

**Test 1: Direct API call**
```bash
curl "http://api.openweathermap.org/data/2.5/weather?q=London&appid=YOUR_NEW_KEY&units=metric"
```

**Success looks like:**
```json
{
  "coord": {...},
  "weather": [{"main": "Clouds", ...}],
  "main": {"temp": 15, ...}
}
```

**Error looks like:**
```json
{"cod":401, "message": "Invalid API key"}
```

**Test 2: In your app**
```bash
php artisan tinker
```
```php
$service = new App\Services\WeatherService();
$weather = $service->getCurrentWeather('London');
var_dump($weather);
// Should show array with weather data, not NULL
```

**Test 3: In browser**
- Visit: `http://localhost:8000/tasks`
- You should see weather widget (not the warning message)

---

## Important Notes

### ⏰ API Key Activation Time
- **New keys take 10-60 minutes to activate**
- Be patient! Check back after waiting

### 🔑 Key Format
- Should be ~32 characters long
- Example: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`
- No spaces or special characters (except letters/numbers)

### ✅ Verification Checklist
- [ ] Signed up at openweathermap.org
- [ ] Verified email address
- [ ] Logged into account
- [ ] Copied API key from dashboard
- [ ] Updated `.env` file
- [ ] Ran `php artisan config:clear`
- [ ] Waited 10-60 minutes (if new key)
- [ ] Tested with curl command
- [ ] Refreshed browser

---

## Troubleshooting

### Still seeing "Weather unavailable"?
1. **Check .env file:**
   ```bash
   grep OPENWEATHER_API_KEY .env
   ```
   Should show your key (not the old one)

2. **Verify config:**
   ```bash
   php artisan tinker
   >>> config('services.openweather.api_key')
   ```
   Should return your key, not `null`

3. **Check logs:**
   ```bash
   tail -20 storage/logs/laravel.log | grep weather
   ```
   Look for error messages

4. **Test API directly:**
   ```bash
   curl "http://api.openweathermap.org/data/2.5/weather?q=London&appid=YOUR_KEY&units=metric"
   ```
   If this fails, the key is invalid

### "Invalid API key" Error?
- Key might not be activated yet (wait 10-60 min)
- Key might be copied incorrectly (check for spaces)
- Account might not be verified (check email)

### Key Works in curl but not in app?
- Clear config cache: `php artisan config:clear`
- Restart your server: `php artisan serve`
- Check `.env` file has no extra spaces

---

## Once It Works

You'll see a **blue weather widget** on your `/tasks` page showing:
- 🌤️ Weather icon
- City name (London by default)
- Temperature in Celsius
- Weather description
- Humidity and wind speed

**Enjoy!** 🎉

