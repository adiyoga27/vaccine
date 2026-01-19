# AGENTS.md - TANDU GEMAS Development Guide

## Build, Lint, and Test Commands

### PHP Dependencies
```bash
# Install dependencies
composer install

# Run development server
php artisan serve

# Run with queue, logs, and vite concurrently
composer dev
```

### Frontend Assets
```bash
# Install frontend dependencies
npm install

# Build for production
npm run build

# Run development server (Vite)
npm run dev
```

### Code Formatting (Laravel Pint)
```bash
# Format all PHP files
./vendor/bin/pint

# Format with verbose output
./vendor/bin/pint -v

# Check formatting without modifying files
./vendor/bin/pint --test
```

### Testing
```bash
# Run all tests
composer test
./vendor/bin/phpunit

# Run a single test file
./vendor/bin/phpunit tests/Unit/ExampleTest.php

# Run a single test by name
./vendor/bin/phpunit --filter test_example_feature

# Run tests with coverage
./vendor/bin/phpunit --coverage-html coverage
```

### Database
```bash
# Run migrations
php artisan migrate

# Run migrations with seed data
php artisan migrate:fresh --seed

# Clear configuration cache
php artisan config:clear
```

## Code Style Guidelines

### General Principles
- Follow PSR-12 PHP coding standard (enforced by Laravel Pint)
- Use strict typing: `declare(strict_types=1);`
- Use PHP 8.2 features when appropriate (typed properties, constructor promotion)
- Keep methods focused and under 50 lines when possible

### Naming Conventions
| Element | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `AdminController`, `VaccinePatient` |
| Methods | camelCase | `storeVillage()`, `destroyPosyandu()` |
| Variables | camelCase | `$vaccineSchedule`, `$patientAgeMonths` |
| Constants | SCREAMING_SNAKE_CASE | `DB_CONNECTION` |
| Database tables | snake_case | `vaccine_patients`, `vaccine_schedules` |
| Columns | snake_case | `date_birth`, `mother_name` |
| Routes | kebab-case with dots | `admin.villages.store` |
| Views | kebab-case | `dashboard/admin/villages/index.blade.php` |

### File Structure
```
app/
  Http/
    Controllers/          # Controller classes only
    Requests/             # Form request classes (not currently used)
  Models/                 # Eloquent models
routes/
  web.php                 # All web routes (auth, admin, user)
tests/
  Unit/                   # Unit tests
  Feature/                # Feature/integration tests
```

### Code Formatting
- Indentation: 4 spaces
- Line endings: LF (Unix)
- Always add trailing newline
- No trailing whitespace
- Use strict comparisons: `===` and `!==`

### Imports and Namespaces
```php
// Standard import pattern
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Use fully qualified class names for route definitions
Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users']);
```

### Controller Conventions
- Extend `App\Http\Controllers\Controller` base class
- Use dependency injection for request validation
- Return redirect with success/error messages
- Use route model binding for model parameters
```php
public function updateVillage(Request $request, Village $village)
{
    $request->validate(['name' => 'required']);
    $village->update($request->except(['_token', '_method']));
    return back()->with('success', 'Desa berhasil diperbarui');
}
```

### Model Conventions
- Define `$fillable` for mass-assignable attributes
- Define `$hidden` for attributes that should be hidden from arrays
- Use `casts()` method for type casting
- Define relationships as methods returning `HasOne`, `BelongsTo`, etc.
```php
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }
}
```

### Route Conventions
- Use route names: `->name('admin.villages.store')`
- Use route groups with middleware and prefixes
- Use route model binding for model parameters
- Define all routes in `routes/web.php`

### Error Handling
- Use Laravel validation rules in controllers
- Return appropriate HTTP status codes
- Use `firstOrFail()` for single model lookups
- Use `findOrFail()` for ID-based lookups
- Wrap database transactions in `DB::transaction()`

### Blade Templates
- Use kebab-case for filenames: `villages/index.blade.php`
- Use `compact()` to pass data to views
- Use `with()` for flash messages
- Follow Tailwind CSS class conventions

### Database Migrations
- Use descriptive table names (plural, snake_case)
- Include `deleted_at` for soft deletes when needed
- Use `unsigned()` for foreign key references
- Set default values appropriately

### Testing
- Follow Laravel testing conventions
- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- Use `TestCase.php` as base class
- Group related tests with `@test` or test methods starting with `test_`

### Additional Notes
- This is a vaccination management system for Indonesian Posyandu (maternal health posts)
- All user-facing strings are in Indonesian
- Uses `spatie/laravel-activitylog` for audit logging
- Primary database is MySQL, tests use SQLite in-memory
