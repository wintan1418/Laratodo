# OpenWeatherMap API Key Setup

## ✅ Setup Complete!

Your API key has been added to `.env`:
```
OPENWEATHER_API_KEY=67a3f1728c6a36171b6996f53630c8d5
```

## ⚠️ API Key Status

The API key appears to be invalid or not activated yet. Here's how to fix it:

### Step 1: Verify Your API Key

1. **Visit:** https://openweathermap.org/api
2. **Log in** to your account
3. **Go to:** API Keys section (in your account dashboard)
4. **Check:** Is your key activated?
   - New API keys take **10-60 minutes** to activate
   - Make sure you've verified your email

### Step 2: Get a New Key (If Needed)

If your key doesn't work:

1. **Visit:** https://home.openweathermap.org/api_keys
2. **Click:** "Generate" to create a new key
3. **Wait:** 10-60 minutes for activation
4. **Update** `.env` with the new key:
   ```bash
   # Edit .env file
   OPENWEATHER_API_KEY=your_new_key_here
   ```
5. **Clear config:**
   ```bash
   php artisan config:clear
   ```

### Step 3: Test Your Key

Test if your key works:
```bash
curl "http://api.openweathermap.org/data/2.5/weather?q=London&appid=YOUR_API_KEY&units=metric"
```

**Success response** looks like:
```json
{
  "coord": {...},
  "weather": [...],
  "main": {"temp": 15, ...}
}
```

**Error response** looks like:
```json
{"cod":401, "message": "Invalid API key"}
```

### Step 4: Once Key Works

1. **Visit:** `http://localhost:8000/tasks`
2. **You should see:** Weather widget at the top
3. **Or test API:** `http://localhost:8000/api/weather`

---

## 🔧 Current Configuration

✅ **Config file:** `config/services.php` - Already configured  
✅ **Environment:** `.env` - API key added  
✅ **Cache:** Cleared  

## 📝 Free Tier Limits

- **60 calls/minute**
- **1,000,000 calls/month**
- **Perfect for learning!**

---

## 🐛 Troubleshooting

### "Invalid API key" Error
- ✅ Wait 10-60 minutes after creating key
- ✅ Verify your email address
- ✅ Check key is copied correctly (no spaces)

### "API key not configured" Warning
- ✅ Check `.env` file has `OPENWEATHER_API_KEY=...`
- ✅ Run: `php artisan config:clear`
- ✅ Make sure no spaces around `=`

### Weather Not Showing
- ✅ Check browser console for errors
- ✅ Check `storage/logs/laravel.log`
- ✅ Verify API key works with curl (see Step 3)

---

## 🎯 Quick Test

Once your API key is activated:

```bash
# Test in browser
http://localhost:8000/tasks

# Test API endpoint
http://localhost:8000/api/weather

# Test with different city
http://localhost:8000/api/weather?city=Paris
```

---

**Your setup is correct!** Just need to wait for API key activation or get a valid key. 🚀

