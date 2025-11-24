# Laravel Authorization Guide

## Understanding Authorization vs Authentication

**Authentication** = "Who are you?" (Login/Logout)
- Verifies user identity
- Checks if user is logged in
- Uses sessions, tokens, etc.

**Authorization** = "What can you do?" (Permissions)
- Checks if user has permission to perform an action
- Determines access rights
- Uses Gates and Policies

---

## How Authorization Works in Laravel

Laravel provides **two main ways** to handle authorization:

### 1. Gates (Simple Closures)
Gates are closures that define authorization logic. Good for simple checks not tied to a model.

**Example:**
```php
// In AppServiceProvider or AuthServiceProvider
Gate::define('update-post', function (User $user, Post $post) {
    return $user->id === $post->user_id;
});

// Use in controller
if (Gate::allows('update-post', $post)) {
    // User can update
}
```

### 2. Policies (Recommended for Models)
Policies are classes that group authorization logic around a specific model. **This is what we use in our Task app!**

---

## Our Task Authorization Implementation

### Step 1: Policy Creation

We created `TaskPolicy` using:
```bash
php artisan make:policy TaskPolicy --model=Task
```

### Step 2: Policy Registration

Laravel **automatically discovers** policies! No manual registration needed in Laravel 12.

**How it works:**
- Laravel looks for policies in `app/Policies/`
- Policy name must match model name + "Policy"
- `TaskPolicy` automatically handles `Task` model

### Step 3: Policy Methods

Our `TaskPolicy` has these methods:

```php
// Can user view ANY tasks?
public function viewAny(User $user): bool
{
    return true; // All authenticated users can view their tasks
}

// Can user view THIS specific task?
public function view(User $user, Task $task): bool
{
    return $user->id === $task->user_id; // Only owner
}

// Can user create tasks?
public function create(User $user): bool
{
    return true; // All authenticated users can create
}

// Can user update THIS task?
public function update(User $user, Task $task): bool
{
    return $user->id === $task->user_id; // Only owner
}

// Can user delete THIS task?
public function delete(User $user, Task $task): bool
{
    return $user->id === $task->user_id; // Only owner
}
```

### Step 4: Using Policies in Controllers

**Method 1: Using `authorize()`**
```php
public function show(Task $task): View
{
    $this->authorize('view', $task);
    // If unauthorized, Laravel throws 403 Forbidden
    return view('tasks.show', compact('task'));
}
```

**Method 2: Using `Gate::allows()`**
```php
if (Gate::allows('view', $task)) {
    // User can view
}
```

**Method 3: Using `Gate::denies()`**
```php
if (Gate::denies('update', $task)) {
    abort(403, 'Unauthorized action.');
}
```

**Method 4: Using `can()` on User model**
```php
if ($user->can('view', $task)) {
    // User can view
}
```

### Step 5: Authorization Flow

1. **User requests** `/tasks/5`
2. **Route Model Binding** automatically loads `Task::find(5)`
3. **Controller calls** `$this->authorize('view', $task)`
4. **Laravel checks** `TaskPolicy::view($user, $task)`
5. **Policy returns** `true` or `false`
6. **If false** → 403 Forbidden error
7. **If true** → Request continues

---

## Authorization Best Practices

### 1. Always Check Ownership
```php
public function update(User $user, Task $task): bool
{
    return $user->id === $task->user_id;
}
```

### 2. Use Route Model Binding
```php
// ✅ Good - Laravel automatically loads and checks
public function show(Task $task) {
    $this->authorize('view', $task);
}

// ❌ Bad - Manual loading
public function show($id) {
    $task = Task::findOrFail($id);
    $this->authorize('view', $task);
}
```

### 3. Filter at Query Level
```php
// ✅ Good - Only loads user's tasks
$tasks = Auth::user()->tasks()->get();

// ❌ Bad - Loads all tasks, filters in PHP
$tasks = Task::all()->filter(fn($task) => $task->user_id === Auth::id());
```

### 4. Use Policies for Model Actions
- Policies are better for model-specific authorization
- Gates are better for general application features

---

## Advanced Authorization

### Custom Authorization Responses

You can return custom messages:

```php
use Illuminate\Auth\Access\Response;

public function update(User $user, Task $task): Response
{
    return $user->id === $task->user_id
        ? Response::allow()
        : Response::deny('You can only edit your own tasks.');
}
```

### Before Hooks

Run code before all policy checks:

```php
// In AppServiceProvider
Gate::before(function (User $user, string $ability) {
    if ($user->isAdmin()) {
        return true; // Admins can do everything
    }
});
```

### After Hooks

Run code after all policy checks:

```php
Gate::after(function (User $user, string $ability, $result) {
    // Log authorization attempts
});
```

---

## Testing Authorization

```php
// In tests
$user = User::factory()->create();
$task = Task::factory()->create(['user_id' => $user->id]);
$otherUser = User::factory()->create();

// Test authorized access
$this->actingAs($user)
    ->get("/tasks/{$task->id}")
    ->assertStatus(200);

// Test unauthorized access
$this->actingAs($otherUser)
    ->get("/tasks/{$task->id}")
    ->assertStatus(403);
```

---

## Common Authorization Patterns

### 1. Owner Only
```php
return $user->id === $task->user_id;
```

### 2. Admin Override
```php
return $user->isAdmin() || $user->id === $task->user_id;
```

### 3. Role-Based
```php
return $user->hasRole('manager') || $user->id === $task->user_id;
```

### 4. Time-Based
```php
return $user->id === $task->user_id 
    && $task->created_at->isAfter(now()->subDays(7));
```

---

## Summary

**Authorization in our app:**
- ✅ Uses **Policies** (TaskPolicy)
- ✅ Automatically registered (Laravel 12)
- ✅ Checks ownership (`user_id` matching)
- ✅ Applied in controllers with `authorize()`
- ✅ Prevents unauthorized access (403 errors)

**Key Files:**
- `app/Policies/TaskPolicy.php` - Authorization rules
- `app/Http/Controllers/TaskController.php` - Uses `authorize()`
- `app/Http/Controllers/Controller.php` - Has `AuthorizesRequests` trait

