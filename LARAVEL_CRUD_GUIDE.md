# Laravel CRUD Application - Learning Guide

Welcome! This guide will help you understand how the Task CRUD (Create, Read, Update, Delete) application works and teach you Laravel fundamentals.

## Table of Contents

1. [What is CRUD?](#what-is-crud)
2. [Laravel Project Structure](#laravel-project-structure)
3. [Understanding MVC Pattern](#understanding-mvc-pattern)
4. [Step-by-Step Breakdown](#step-by-step-breakdown)
5. [Key Laravel Concepts](#key-laravel-concepts)
6. [Authentication & Authorization](#authentication--authorization)
7. [How to Use This Application](#how-to-use-this-application)

---

## What is CRUD?

CRUD stands for:
- **Create** - Add new records
- **Read** - View/List records
- **Update** - Edit existing records
- **Delete** - Remove records

This is the foundation of most web applications!

---

## Laravel Project Structure

Here's how Laravel organizes files:

```
example-app/
├── app/                    # Your application code
│   ├── Http/
│   │   ├── Controllers/   # Handle requests (TaskController.php)
│   │   └── Requests/      # Form validation (StoreTaskRequest.php)
│   └── Models/            # Database models (Task.php)
├── database/
│   ├── migrations/        # Database schema definitions
│   ├── factories/         # Generate fake data for testing
│   └── seeders/           # Populate database with data
├── resources/
│   └── views/             # Blade templates (HTML)
│       ├── layouts/       # Reusable layouts
│       └── tasks/         # Task-specific views
├── routes/
│   └── web.php            # Web routes (URLs)
└── config/                # Configuration files
```

---

## Understanding MVC Pattern

Laravel follows the **MVC (Model-View-Controller)** pattern:

### Model (`app/Models/Task.php`)
- Represents data structure
- Interacts with database
- Contains business logic

**Example:**
```php
class Task extends Model
{
    protected $fillable = ['title', 'description', 'completed'];
}
```

### View (`resources/views/tasks/index.blade.php`)
- What users see (HTML)
- Displays data from controller
- Uses Blade templating engine

**Example:**
```blade
@foreach($tasks as $task)
    <p>{{ $task->title }}</p>
@endforeach
```

### Controller (`app/Http/Controllers/TaskController.php`)
- Handles user requests
- Processes data
- Returns views or redirects

**Example:**
```php
public function index()
{
    $tasks = Task::latest()->paginate(10);
    return view('tasks.index', compact('tasks'));
}
```

---

## Step-by-Step Breakdown

### 1. Database Migration (`database/migrations/..._create_tasks_table.php`)

**What it does:** Defines the database table structure.

**Key concepts:**
- `up()` - Creates the table
- `down()` - Drops the table (for rollback)
- `Schema::create()` - Creates table with columns

**Our Task table has:**
- `id` - Primary key (auto-increment)
- `title` - String (required)
- `description` - Text (optional)
- `completed` - Boolean (default: false)
- `timestamps` - Created/updated dates (automatic)

**To run:** `php artisan migrate`

---

### 2. Model (`app/Models/Task.php`)

**What it does:** Represents a Task in your application.

**Key concepts:**
- `$fillable` - Fields that can be mass-assigned (security)
- `casts()` - Converts data types (e.g., boolean)
- `HasFactory` - Allows creating fake data for testing

**Example usage:**
```php
// Create a task
Task::create(['title' => 'Learn Laravel', 'completed' => false]);

// Get all tasks
$tasks = Task::all();

// Find a task
$task = Task::find(1);
```

---

### 3. Controller (`app/Http/Controllers/TaskController.php`)

**What it does:** Handles HTTP requests and returns responses.

**CRUD Methods:**

#### Index (Read All)
```php
public function index(): View
{
    $tasks = Task::latest()->paginate(10);
    return view('tasks.index', compact('tasks'));
}
```
- Gets all tasks, ordered by newest first
- Paginates results (10 per page)
- Returns the index view

#### Create (Show Form)
```php
public function create(): View
{
    return view('tasks.create');
}
```
- Shows the form to create a new task

#### Store (Save New)
```php
public function store(StoreTaskRequest $request): RedirectResponse
{
    Task::create($request->validated());
    return redirect()->route('tasks.index')
        ->with('success', 'Task created successfully.');
}
```
- Validates input using `StoreTaskRequest`
- Creates new task
- Redirects with success message

#### Show (Read One)
```php
public function show(Task $task): View
{
    return view('tasks.show', compact('task'));
}
```
- Uses **Route Model Binding** - Laravel automatically finds the task by ID
- Shows single task details

#### Edit (Show Edit Form)
```php
public function edit(Task $task): View
{
    return view('tasks.edit', compact('task'));
}
```
- Shows form pre-filled with task data

#### Update (Save Changes)
```php
public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
{
    $task->update($request->validated());
    return redirect()->route('tasks.index')
        ->with('success', 'Task updated successfully.');
}
```
- Validates input
- Updates existing task
- Redirects with success message

#### Destroy (Delete)
```php
public function destroy(Task $task): RedirectResponse
{
    $task->delete();
    return redirect()->route('tasks.index')
        ->with('success', 'Task deleted successfully.');
}
```
- Deletes the task
- Redirects with success message

---

### 4. Form Requests (`app/Http/Requests/StoreTaskRequest.php`)

**What it does:** Validates form input before processing.

**Key concepts:**
- `authorize()` - Check if user can perform action (we return `true` for now)
- `rules()` - Validation rules
- `messages()` - Custom error messages

**Our validation rules:**
```php
'title' => ['required', 'string', 'max:255'],
'description' => ['nullable', 'string'],
'completed' => ['boolean'],
```

**Common validation rules:**
- `required` - Field must be present
- `string` - Must be a string
- `max:255` - Maximum 255 characters
- `nullable` - Field is optional
- `boolean` - Must be true/false

---

### 5. Routes (`routes/web.php`)

**What it does:** Maps URLs to controller methods.

**Resource Route:**
```php
Route::resource('tasks', TaskController::class);
```

This single line creates **7 routes** automatically:

| Method | URL | Controller Method | Purpose |
|--------|-----|-------------------|---------|
| GET | `/tasks` | index() | List all tasks |
| GET | `/tasks/create` | create() | Show create form |
| POST | `/tasks` | store() | Save new task |
| GET | `/tasks/{task}` | show() | Show one task |
| GET | `/tasks/{task}/edit` | edit() | Show edit form |
| PUT | `/tasks/{task}` | update() | Update task |
| DELETE | `/tasks/{task}` | destroy() | Delete task |

**Route Model Binding:**
When you use `{task}` in the route, Laravel automatically finds the Task model with that ID!

---

### 6. Views (Blade Templates)

**What it does:** Generates HTML that users see.

**Key Blade syntax:**
- `{{ $variable }}` - Output variable (escaped for security)
- `@if/@endif` - Conditional statements
- `@foreach/@endforeach` - Loop through arrays
- `@extends('layout')` - Inherit from layout
- `@section('content')` - Define content section
- `@csrf` - CSRF protection token
- `@method('PUT')` - HTTP method spoofing (for PUT/DELETE)

**Layout (`resources/views/layouts/app.blade.php`):**
- Base template that all pages extend
- Contains navigation, common HTML structure
- Uses `@yield('content')` to insert page content

**Task Views:**
- `index.blade.php` - Lists all tasks in a table
- `create.blade.php` - Form to create new task
- `edit.blade.php` - Form to edit existing task
- `show.blade.php` - Display single task details

---

## Key Laravel Concepts

### 1. Eloquent ORM
Laravel's database toolkit. Instead of writing SQL, you use PHP:

```php
// Instead of: SELECT * FROM tasks WHERE completed = 0
Task::where('completed', false)->get();

// Instead of: INSERT INTO tasks...
Task::create(['title' => 'New Task']);
```

### 2. Mass Assignment Protection
Only fields in `$fillable` can be mass-assigned:

```php
// ✅ Allowed (title is in $fillable)
Task::create(['title' => 'Task']);

// ❌ Not allowed (hacker_field is not in $fillable)
Task::create(['hacker_field' => 'bad data']);
```

### 3. Route Model Binding
Laravel automatically finds models:

```php
// URL: /tasks/5
// Laravel automatically does: Task::findOrFail(5)
public function show(Task $task) {
    // $task is already loaded!
}
```

### 4. CSRF Protection
Laravel protects against Cross-Site Request Forgery attacks. All forms need `@csrf`:

```blade
<form method="POST">
    @csrf  <!-- Required! -->
    ...
</form>
```

### 5. Flashing Data to Session
Temporary messages that disappear after one request:

```php
return redirect()->route('tasks.index')
    ->with('success', 'Task created!');
```

Then in view:
```blade
@if(session('success'))
    {{ session('success') }}
@endif
```

### 6. Pagination
Laravel makes pagination easy:

```php
$tasks = Task::paginate(10); // 10 per page
```

In view:
```blade
{{ $tasks->links() }} <!-- Automatic pagination links -->
```

---

## Authentication & Authorization

This application uses **Laravel Breeze** for authentication and implements proper authorization to ensure users can only access their own tasks.

### What is Authentication?

**Authentication** = "Who are you?" (Login/Logout)
- Users must log in to access tasks
- Each user has their own account
- Sessions track who is logged in

**Authorization** = "What can you do?" (Permissions)
- Users can only view/edit/delete their own tasks
- Policies enforce these rules

### Laravel Breeze

Breeze provides:
- **Registration** - Users can create accounts
- **Login** - Users can sign in
- **Password Reset** - Users can reset forgotten passwords
- **Email Verification** - Optional email verification
- **Profile Management** - Users can update their profile

**Files Created by Breeze:**
- `resources/views/auth/` - Login, register, password reset views
- `app/Http/Controllers/Auth/` - Authentication controllers
- `routes/auth.php` - Authentication routes
- `app/View/Components/AppLayout.php` - Main layout component

### How Authentication Works

#### 1. Middleware Protection

Controllers use middleware to require authentication:

```php
public function __construct()
{
    $this->middleware('auth');
}
```

This ensures only logged-in users can access task routes.

#### 2. User-Task Relationship

**Database Migration:**
```php
$table->foreignId('user_id')->constrained()->onDelete('cascade');
```

**Task Model:**
```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

**User Model:**
```php
public function tasks()
{
    return $this->hasMany(Task::class);
}
```

#### 3. Filtering by User

Tasks are automatically filtered to show only the logged-in user's tasks:

```php
public function index(): View
{
    $tasks = Auth::user()->tasks()->latest()->paginate(10);
    return view('tasks.index', compact('tasks'));
}
```

#### 4. Authorization Policies

**TaskPolicy** (`app/Policies/TaskPolicy.php`) controls who can do what:

```php
public function view(User $user, Task $task): bool
{
    return $user->id === $task->user_id; // Only owner can view
}

public function update(User $user, Task $task): bool
{
    return $user->id === $task->user_id; // Only owner can update
}
```

**Using Policies in Controllers:**
```php
public function show(Task $task): View
{
    $this->authorize('view', $task); // Checks policy
    return view('tasks.show', compact('task'));
}
```

### Authentication Flow

1. **User visits `/tasks`** → Redirected to `/login` if not authenticated
2. **User logs in** → Session created, redirected to `/tasks`
3. **User creates task** → `user_id` automatically set to current user
4. **User views tasks** → Only sees their own tasks
5. **User tries to access another's task** → 403 Forbidden error

### Key Authentication Concepts

#### Auth Facade
```php
Auth::user()           // Get current user
Auth::check()          // Check if user is logged in
Auth::id()             // Get current user ID
auth()->user()         // Alternative syntax
```

#### Middleware
- `auth` - Requires authentication
- `guest` - Requires user to NOT be authenticated (for login page)
- `verified` - Requires verified email

#### Route Protection
```php
Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class);
});
```

### Security Features

1. **CSRF Protection** - All forms include `@csrf` token
2. **Password Hashing** - Passwords are automatically hashed
3. **Session Management** - Secure session handling
4. **Route Model Binding** - Automatic model loading with authorization checks
5. **Policy Authorization** - Centralized permission logic

### Creating Tasks with Authentication

When a user creates a task, the `user_id` is automatically set:

```php
public function store(StoreTaskRequest $request): RedirectResponse
{
    Auth::user()->tasks()->create($request->validated());
    // Equivalent to: Task::create(['user_id' => Auth::id(), ...])
    return redirect()->route('tasks.index');
}
```

### Testing Authentication

```bash
# Create a test user
php artisan tinker
>>> User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => bcrypt('password')]);

# Or use factory
>>> User::factory()->create();
```

---

## External API Integration

This application integrates with the **OpenWeatherMap API** to show weather information.

### Quick Overview

- **Service Class**: `app/Services/WeatherService.php` - Handles API calls
- **Controller**: `app/Http/Controllers/WeatherController.php` - API endpoint
- **Display**: Weather widget on tasks page
- **Caching**: 10-minute cache to reduce API calls

### Setup

1. Get API key from https://openweathermap.org/api (free)
2. Add to `.env`: `OPENWEATHER_API_KEY=your_key`
3. Run: `php artisan config:clear`
4. Weather appears on `/tasks` page

**See `LARAVEL_API_INTEGRATION_GUIDE.md` for complete details!**

---

## How to Use This Application

### 1. Run Migrations
```bash
php artisan migrate
```
Creates the `tasks` and `users` tables in your database.

### 2. Start Development Server
```bash
php artisan serve
```
Visit: `http://localhost:8000`

### 3. Register an Account
1. Visit `http://localhost:8000/register`
2. Fill in your name, email, and password
3. Click "Register"

### 4. Access the Application
After logging in, you can:
- **List Tasks:** `http://localhost:8000/tasks` (shows only your tasks)
- **Create Task:** `http://localhost:8000/tasks/create`
- **View Task:** `http://localhost:8000/tasks/1`
- **Edit Task:** `http://localhost:8000/tasks/1/edit`

### 5. (Optional) Seed Sample Data
```bash
php artisan tinker
>>> $user = User::first();
>>> Task::factory(10)->create(['user_id' => $user->id]);
```
Creates 10 sample tasks for the first user.

---

## Common Laravel Commands

```bash
# Create a model with migration, factory, and seeder
php artisan make:model Task -mfs

# Create a controller
php artisan make:controller TaskController

# Create a resource controller (with CRUD methods)
php artisan make:controller TaskController --resource

# Create a form request
php artisan make:request StoreTaskRequest

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Clear cache
php artisan config:clear
php artisan cache:clear

# List all routes
php artisan route:list
```

---

## Next Steps to Learn

1. ✅ **Authentication** - User login/logout (COMPLETED)
2. ✅ **Relationships** - Tasks linked to users (COMPLETED)
3. ✅ **Middleware** - Authentication checks (COMPLETED)
4. ✅ **Authorization** - Policies for task access (COMPLETED)
5. **API Development** - Create REST API endpoints
6. **Testing** - Write tests for your application
7. **Events & Listeners** - Handle task completion events
8. **Queues** - Process tasks in background
9. **File Uploads** - Add attachments to tasks
10. **Email Notifications** - Send emails when tasks are completed

---

## Tips for Learning Laravel

1. **Read the Documentation** - Laravel docs are excellent: https://laravel.com/docs
2. **Use Tinker** - Test code interactively: `php artisan tinker`
3. **Check Routes** - See all routes: `php artisan route:list`
4. **Read Error Messages** - Laravel errors are very helpful
5. **Use IDE Autocomplete** - Install Laravel IDE Helper
6. **Practice** - Build small projects to reinforce concepts

---

## File Reference

Here are the files created for this CRUD application:

**Models:**
- `app/Models/Task.php`

**Controllers:**
- `app/Http/Controllers/TaskController.php`

**Form Requests:**
- `app/Http/Requests/StoreTaskRequest.php`
- `app/Http/Requests/UpdateTaskRequest.php`

**Migrations:**
- `database/migrations/2025_11_24_161122_create_tasks_table.php`
- `database/migrations/2025_11_24_182538_add_user_id_to_tasks_table.php`

**Policies:**
- `app/Policies/TaskPolicy.php`

**Factories:**
- `database/factories/TaskFactory.php`

**Seeders:**
- `database/seeders/TaskSeeder.php`

**Views:**
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `resources/views/tasks/index.blade.php`
- `resources/views/tasks/create.blade.php`
- `resources/views/tasks/edit.blade.php`
- `resources/views/tasks/show.blade.php`
- `resources/views/auth/` (Login, Register, Password Reset - from Breeze)

**Routes:**
- `routes/web.php`
- `routes/auth.php` (Authentication routes - from Breeze)

---

Happy Learning! 🚀

