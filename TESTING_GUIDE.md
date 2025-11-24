# Testing Guide - How to Access New Features

This guide shows you how to test all the features we've added to your Laravel Task application.

---

## 🚀 Quick Start

### 1. Make sure your server is running
```bash
php artisan serve
```
Visit: `http://localhost:8000`

### 2. Make sure you're logged in
- Visit `/register` to create an account
- Or `/login` if you already have one

---

## 🌐 Feature 1: Weather API Integration

### What it does:
Shows current weather information on your tasks page.

### How to test:

#### Step 1: Get a free API key
1. Visit: https://openweathermap.org/api
2. Click "Sign Up" (it's free!)
3. After signing up, go to your API keys section
4. Copy your API key (starts with something like `abc123...`)

#### Step 2: Add API key to your app
1. Open your `.env` file
2. Add this line:
   ```env
   OPENWEATHER_API_KEY=your_api_key_here
   ```
   Replace `your_api_key_here` with your actual key.

#### Step 3: Clear config cache
```bash
php artisan config:clear
```

#### Step 4: View the weather!
1. Visit: `http://localhost:8000/tasks`
2. You should see a weather widget at the top showing:
   - City name (default: London)
   - Temperature in Celsius
   - Weather description
   - Humidity and wind speed

#### Step 5: Test the API endpoint directly
Visit: `http://localhost:8000/api/weather`

You should see JSON response like:
```json
{
  "city": "London",
  "country": "GB",
  "temperature": 15,
  "feels_like": 14,
  "description": "Cloudy",
  "icon": "02d",
  "humidity": 65,
  "wind_speed": 3.5
}
```

#### Test with different city:
Visit: `http://localhost:8000/api/weather?city=New York`

---

## ✅ Feature 2: Task Completion Toggle

### What it does:
Quick button to mark tasks as completed or pending.

### How to test:

#### On Tasks List Page (`/tasks`):
1. Look at any task row
2. You'll see a checkmark icon button
3. Click it to toggle completion status
4. The badge will change:
   - ✅ Green "Completed" badge
   - ⏳ Yellow "Pending" badge

#### On Task Detail Page (`/tasks/{id}`):
1. Click "View" on any task
2. At the top, you'll see:
   - "Mark Completed" button (if pending)
   - "Mark Pending" button (if completed)
3. Click to toggle status
4. You'll see a success message

#### Visual indicators:
- **Completed tasks**: Green badge, filled checkmark icon
- **Pending tasks**: Yellow badge, outline checkmark icon

---

## 🔒 Feature 3: Authorization (Policies)

### What it does:
Ensures users can only access their own tasks.

### How to test:

#### Test 1: Normal Access (Should Work)
1. Log in as User A
2. Create a task
3. View your task: `/tasks/1` ✅ Should work
4. Edit your task: `/tasks/1/edit` ✅ Should work
5. Delete your task ✅ Should work

#### Test 2: Unauthorized Access (Should Fail)
1. **Create a second user account:**
   - Log out
   - Register a new account with different email
   - Or use tinker:
   ```bash
   php artisan tinker
   >>> User::create(['name' => 'Test User', 'email' => 'test2@example.com', 'password' => bcrypt('password')]);
   ```

2. **Try to access User A's task:**
   - Log in as User B
   - Try to visit: `/tasks/1` (assuming task 1 belongs to User A)
   - **Expected:** 403 Forbidden error page
   - **Message:** "This action is unauthorized"

3. **Try to edit User A's task:**
   - Visit: `/tasks/1/edit`
   - **Expected:** 403 Forbidden error

4. **Try to delete User A's task:**
   - Try to delete task 1
   - **Expected:** 403 Forbidden error

#### Test 3: Verify Task Filtering
1. Log in as User A
2. Create 3 tasks
3. Log out
4. Log in as User B
5. Visit `/tasks`
6. **Expected:** You should only see User B's tasks (if any)
7. User A's tasks should NOT appear

---

## 📋 Complete Testing Checklist

### Authentication Features
- [ ] Can register new account
- [ ] Can login with credentials
- [ ] Can logout
- [ ] Cannot access `/tasks` without login (redirects to `/login`)

### Task CRUD Features
- [ ] Can view list of tasks (`/tasks`)
- [ ] Can create new task (`/tasks/create`)
- [ ] Can view task details (`/tasks/{id}`)
- [ ] Can edit task (`/tasks/{id}/edit`)
- [ ] Can delete task
- [ ] Can toggle task completion status

### Authorization Features
- [ ] Can only see own tasks
- [ ] Cannot view other user's task (403 error)
- [ ] Cannot edit other user's task (403 error)
- [ ] Cannot delete other user's task (403 error)

### Weather API Features
- [ ] Weather widget appears on `/tasks` page
- [ ] Weather shows correct city (London by default)
- [ ] Weather shows temperature
- [ ] Weather shows description
- [ ] API endpoint `/api/weather` returns JSON
- [ ] Can change city via query parameter

### UI/UX Features
- [ ] Success messages appear after actions
- [ ] Error messages appear for validation failures
- [ ] Navigation menu works
- [ ] Responsive design (try mobile view)
- [ ] Dark mode works (if applicable)

---

## 🧪 Testing with Tinker

### Create test users:
```bash
php artisan tinker
```

```php
// Create User A
$userA = User::create([
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'password' => bcrypt('password')
]);

// Create User B
$userB = User::create([
    'name' => 'Bob',
    'email' => 'bob@example.com',
    'password' => bcrypt('password')
]);

// Create tasks for User A
$userA->tasks()->create(['title' => 'Task 1', 'description' => 'Test task']);
$userA->tasks()->create(['title' => 'Task 2', 'description' => 'Another task']);

// Create task for User B
$userB->tasks()->create(['title' => 'Bob\'s Task', 'description' => 'Bob\'s task']);
```

### Test authorization:
```php
// Check if user owns task
$task = Task::find(1);
$userA->can('view', $task); // Should return true if userA owns task
$userB->can('view', $task); // Should return false
```

---

## 🐛 Troubleshooting

### Weather not showing?
1. **Check API key:**
   ```bash
   php artisan tinker
   >>> config('services.openweather.api_key')
   ```
   Should return your API key, not `null`.

2. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Look for weather API errors.

3. **Test API manually:**
   ```bash
   curl "http://api.openweathermap.org/data/2.5/weather?q=London&appid=YOUR_API_KEY&units=metric"
   ```

### Authorization not working?
1. **Check policy is registered:**
   ```bash
   php artisan route:list | grep tasks
   ```
   Should show all task routes.

2. **Check user is logged in:**
   ```php
   // In tinker
   Auth::check() // Should return true when logged in
   Auth::user() // Should return current user
   ```

3. **Check task ownership:**
   ```php
   $task = Task::find(1);
   $task->user_id; // Should match Auth::id()
   ```

### Toggle button not working?
1. **Check route exists:**
   ```bash
   php artisan route:list | grep toggle
   ```
   Should show `tasks.toggle` route.

2. **Check CSRF token:**
   - Make sure `@csrf` is in the form
   - Check browser console for errors

---

## 📍 URLs to Test

### Main Pages
- `/` - Redirects to tasks
- `/login` - Login page
- `/register` - Registration page
- `/tasks` - Tasks list (requires login)
- `/tasks/create` - Create task (requires login)

### Task Actions
- `/tasks/1` - View task #1 (requires login + ownership)
- `/tasks/1/edit` - Edit task #1 (requires login + ownership)
- `/tasks/1/toggle` - Toggle completion (POST, requires login + ownership)

### API Endpoints
- `/api/weather` - Weather JSON (requires login)
- `/api/weather?city=Paris` - Weather for specific city

### Profile
- `/profile` - Edit profile (requires login)

---

## 🎯 Quick Test Script

Run this to quickly test everything:

```bash
# 1. Start server
php artisan serve

# 2. In another terminal, test routes
php artisan route:list

# 3. Check if migrations ran
php artisan migrate:status

# 4. Create test data (optional)
php artisan db:seed --class=TaskSeeder
```

Then visit `http://localhost:8000` and follow the checklist above!

---

Happy Testing! 🚀

