# Properos Users Package

Laravel 11 package for user management, authentication, authorization (roles & permissions), and user profiles.

## Requirements

- **Laravel**: 11.x
- **PHP**: 8.2+
- **Database**: MySQL 5.7+ or MariaDB 10.3+

## Dependencies

This package requires the following packages:

| Package | Version | Purpose |
|---------|---------|---------|
| `laravel/sanctum` | ^4.0 | API authentication |
| `intervention/image` | ^3.0 | Image processing (avatars, uploads) |
| `intervention/image-laravel` | ^1.0 | Laravel integration for Intervention Image |
| `laravel/ui` | ^4.0 | Authentication scaffolding |
| `laravel/socialite` | ^5.0 | Social authentication (optional) |

## Installation

### 1. Install the package

```bash
composer require properos/laravel-users
```

### 2. Publish configuration files

```bash
php artisan vendor:publish --provider="Properos\Users\UsersServiceProvider"
```

### 3. Run migrations

```bash
php artisan migrate
```

This will create the following tables:
- `users`
- `user_addresses`
- `user_profiles`
- `roles`
- `permissions`
- `user_roles`
- `user_permissions`
- `role_permissions`
- `api_credentials`
- `user_activity_logs`

### 4. Run seeders (optional)

Add the following to `database/seeders/DatabaseSeeder.php`:

```php
use Properos\Users\Database\Seeders\RolesPermissionsTableSeeder;
use Properos\Users\Database\Seeders\UsersTableSeeder;

public function run(): void
{
    $this->call([
        RolesPermissionsTableSeeder::class,
        UsersTableSeeder::class,
    ]);
}
```

Then run:

```bash
php artisan db:seed
```

## Configuration

### 1. Database Configuration

In `config/database.php`, ensure MySQL configuration uses:

```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => 'InnoDB',
],
```

### 2. Authentication Configuration

In `config/auth.php`, update the guards and providers:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \Properos\Users\Models\User::class,
    ],
],
```

### 3. Service Providers

In `config/app.php`, add to the `providers` array:

```php
Laravel\Socialite\SocialiteServiceProvider::class,
```

### 4. Middleware Registration

In `app/Http/Kernel.php`, register the middleware:

```php
'role' => \Properos\Users\Middlewares\RoleMiddleware::class,
'permission' => \Properos\Users\Middlewares\PermissionMiddleware::class,
'role_or_permission' => \Properos\Users\Middlewares\RoleOrPermissionMiddleware::class,
```

### 5. Social Authentication (Optional)

If you want to enable social authentication (Facebook, Google), install Socialite:

```bash
composer require laravel/socialite
```

Add to `config/services.php`:

```php
'facebook' => [
    'client_id' => env('FACEBOOK_CLIENT_ID'),
    'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
    'redirect' => env('FACEBOOK_CALLBACK', 'http://your-app.com/auth/facebook/callback')
],

'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_CALLBACK', 'http://your-app.com/auth/google/callback')
],
```

Add to your `.env`:

```env
FACEBOOK_CLIENT_ID=your-app-id
FACEBOOK_CLIENT_SECRET=your-app-secret
FACEBOOK_CALLBACK=http://your-app.com/auth/facebook/callback

GOOGLE_CLIENT_ID=your-app-id
GOOGLE_CLIENT_SECRET=your-app-secret
GOOGLE_CALLBACK=http://your-app.com/auth/google/callback
```

### 6. Environment Variables

Add to your `.env`:

```env
# Laravel Mix / Vite
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

MIX_HOST="${DOMAIN}"
MIX_API_BASE_URL=
MIX_CDN_URL=

MIX_ADMIN_WEB_PREFIX='/admin'
MIX_ADMIN_WEB_API_PREFIX='/api/admin'
```

## Asset Compilation

### 1. Install npm dependencies

```bash
npm install
npm install moment --save
```

### 2. Configure webpack.mix.js

Add to `webpack.mix.js`:

```javascript
.js('resources/assets/js/be/modules/users/js/user.js', 'public/be/js/modules/user.js')
```

### 3. Configure bootstrap.js

In `resources/assets/bootstrap.js`, add:

```javascript
import Helpers from './misc/helpers'

window.moment = require('moment')
window.Vue = require('vue');
window.Helpers = Helpers;
```

### 4. Compile assets

```bash
npm run watch
# or
npm run prod
```

## Usage

### Using Models

```php
use Properos\Users\Models\User;
use Properos\Users\Models\Role;
use Properos\Users\Models\Permission;

// Get all users
$users = User::all();

// Find user by ID
$user = User::find(1);

// Get user roles
$roles = $user->roles;

// Get user permissions
$permissions = $user->permissions;
```

### Using Routes

Add to `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', function(){
    return view('be.index');
})->middleware(['auth', 'role:admin']);

Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware(['auth', 'permission:users.view']);
```

### Using Middleware

```php
// Role-based
Route::middleware(['role:admin'])->group(function () {
    // Admin only routes
});

// Permission-based
Route::middleware(['permission:users.edit'])->group(function () {
    // Users edit only routes
});

// Role OR Permission
Route::middleware(['role_or_permission:super-admin'])->group(function () {
    // Routes accessible by super-admin role OR super-admin permission
});
```

### Using Classes

```php
use Properos\Users\Classes\CUser;

$cuser = new CUser();

// Create user
$user = $cuser->create([
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'avatar' => $uploadedFile // Optional UploadedFile
]);

// Assign role
$cuser->assignRole([
    'user_id' => $user->id,
    'role_id' => 1
]);

// Assign permission
$cuser->assignPermission([
    'user_id' => $user->id,
    'permission_id' => 1
]);

// Change password
$cuser->changePassword([
    'user_id' => $user->id,
    'password' => 'newpassword123',
    'password_confirmation' => 'newpassword123'
]);
```

## Features

### User Management
- Create, read, update, delete users
- User profiles and addresses
- Avatar upload and processing
- User activity logging

### Authentication
- Email/password authentication
- Social authentication (Facebook, Google)
- Password reset
- API authentication via Sanctum

### Authorization
- Role-based access control (RBAC)
- Permission-based access control
- Flexible role/permission assignment
- Middleware for route protection

### User Profile
- First name, last name, email, phone
- Company information
- Avatar management
- Address management

### Activity Logging
- Automatic user activity tracking
- Login/logout logging
- Custom action logging

## Troubleshooting

### Migration errors

If you get migration errors about duplicate tables:

```bash
php artisan migrate:rollback --step=1
# Then check if tables exist and drop them if needed
php artisan migrate
```

### Service provider not found

Make sure the service provider is registered in `config/app.php`:

```php
Properos\Users\UsersServiceProvider::class,
```

### Middleware not working

Ensure middleware is registered in `app/Http/Kernel.php` with the correct aliases.

### Image upload not working

1. Check that `intervention/image` and `intervention/image-laravel` are installed
2. Ensure the `public` disk is configured in `config/filesystems.php`
3. Check file permissions on storage/app/public

### Vue components not rendering

1. Ensure assets are compiled: `npm run prod`
2. Check that webpack.mix.js is configured correctly
3. Verify that bootstrap.js includes the required dependencies

### Sanctum authentication not working

1. Ensure `laravel/sanctum` is installed
2. Run `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
3. Add `Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful` to `app/Http/Kernel.php`
4. Configure stateful domains in `config/sanctum.php`

## Breaking Changes from Previous Versions

- **Laravel Version**: Requires Laravel 11.x (minimum)
- **PHP Version**: Requires PHP 8.2+
- **Intervention Image**: Updated to v3 with new API
- **Database Collation**: Default collation is now `utf8_general_ci`

## License

MIT License. See LICENSE file for details.
