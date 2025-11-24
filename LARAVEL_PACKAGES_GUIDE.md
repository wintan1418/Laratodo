# Laravel Packages Guide - Composer vs Gems

## Laravel Uses Composer (Not Gems!)

**Gems** = Ruby/Rails package manager  
**Composer** = PHP/Laravel package manager

Think of Composer as PHP's equivalent to:
- **npm** (Node.js)
- **pip** (Python)
- **gems** (Ruby)
- **Maven** (Java)

---

## What is Composer?

Composer is PHP's **dependency manager**. It:
- Manages PHP packages/libraries
- Handles version conflicts
- Autoloads classes
- Updates dependencies

---

## Key Composer Files

### `composer.json`
Defines your project's dependencies and configuration.

```json
{
    "require": {
        "laravel/framework": "^12.0"
    },
    "require-dev": {
        "laravel/pint": "^1.24"
    }
}
```

**Sections:**
- `require` - Production dependencies
- `require-dev` - Development-only dependencies
- `autoload` - How to load your classes
- `scripts` - Custom commands

### `composer.lock`
Locks exact versions of all dependencies. **Never edit manually!**

---

## Common Composer Commands

### Installing Packages

```bash
# Install a package
composer require vendor/package-name

# Install dev dependency
composer require --dev vendor/package-name

# Install specific version
composer require vendor/package-name:^2.0
```

### Managing Dependencies

```bash
# Install all dependencies
composer install

# Update dependencies
composer update

# Update specific package
composer update vendor/package-name

# Remove package
composer remove vendor/package-name
```

### Autoloading

```bash
# Regenerate autoload files
composer dump-autoload

# Optimize autoloader
composer dump-autoload --optimize
```

---

## How Laravel Uses Composer

### 1. Framework Dependencies
Laravel itself is installed via Composer:
```bash
composer create-project laravel/laravel my-app
```

### 2. Package Discovery
Laravel automatically discovers packages:
- Looks for `composer.json` in packages
- Registers service providers
- Registers facades

### 3. Autoloading
Composer generates `vendor/autoload.php`:
```php
require __DIR__.'/../vendor/autoload.php';
```

This allows you to use classes without manual `require` statements!

---

## Finding Laravel Packages

### Official Laravel Packages
- **Laravel Breeze** - Authentication scaffolding
- **Laravel Sanctum** - API authentication
- **Laravel Horizon** - Queue monitoring
- **Laravel Telescope** - Debugging tool

### Popular Community Packages
- **Spatie** - Many useful packages (permissions, media, etc.)
- **Laravel Excel** - Excel import/export
- **Laravel Debugbar** - Development toolbar
- **Laravel Backup** - Database backups

### Finding Packages
1. **Packagist.org** - Main PHP package repository
2. **Laravel News** - Laravel package reviews
3. **GitHub** - Search for Laravel packages

---

## Installing Packages in Our App

### Example: Installing a Weather API Package

```bash
# Search for packages
composer search weather

# Install a package
composer require guzzlehttp/guzzle

# Or use Laravel's HTTP client (built-in)
# No package needed!
```

---

## Package Structure

When you install a package:

```
vendor/
├── vendor-name/
│   └── package-name/
│       ├── src/          # Source code
│       ├── composer.json
│       └── README.md
└── autoload.php         # Autoloader
```

---

## Laravel Service Providers

Packages register themselves via **Service Providers**:

```php
// In package's ServiceProvider
public function register(): void
{
    $this->app->singleton(WeatherService::class, function () {
        return new WeatherService();
    });
}
```

Laravel 12 auto-discovers providers, so no manual registration needed!

---

## Best Practices

### 1. Version Constraints
```json
"^12.0"  // >= 12.0.0, < 13.0.0
"~12.0"  // >= 12.0.0, < 12.1.0
"12.*"   // Any 12.x version
```

### 2. Don't Commit `vendor/`
The `vendor/` folder is in `.gitignore`. Only commit:
- `composer.json`
- `composer.lock`

### 3. Use `composer.lock` in Production
Always commit `composer.lock` to ensure consistent versions.

### 4. Update Regularly
```bash
composer update --with-dependencies
```

---

## Common Laravel Packages You'll Use

### HTTP Requests
- **Guzzle** - HTTP client (already included in Laravel)
- Laravel has built-in HTTP client: `Http::get('url')`

### Validation
- Laravel has built-in validation (no package needed!)

### File Storage
- **Laravel Storage** - Built-in
- **Spatie Media Library** - Advanced file handling

### API Development
- **Laravel Sanctum** - API authentication
- **Laravel Passport** - OAuth2 server

---

## Summary

**Composer vs Gems:**
- ✅ Laravel uses **Composer** (PHP)
- ❌ Not Gems (that's Ruby/Rails)
- ✅ Similar concept, different tool

**Key Points:**
- `composer.json` = Package manifest
- `composer.lock` = Version lock file
- `composer require` = Install package
- `vendor/` = Installed packages (don't commit)

**In our app:**
- Laravel Framework installed via Composer
- Breeze installed via Composer
- All dependencies managed by Composer

