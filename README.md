## Properos Users

CRUD package.

**Required properos/properos-base package**
**Required laravel/socialite package if want to auth with social accounts**
Configuration => https://laravel.com/docs/5.6/socialite

**Added on config/services.php**
```php
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', '147107342556627'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', 'eda29465e6a4a98b5f89a1c2a3829f14'),
        'redirect' => env('FACEBOOK_CALLBACK','http://properos.com/auth/facebook/callback')
    ],
    
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', '200207622942-nadc5euejp1fb7jj1m13fdhu30ot4icc.apps.googleusercontent.com'),
        'client_secret' => env('GOGOLE_CLIENT_SECRET', 'uhIvqdMkWVD43Lw9EvvsMfQf'),
        'redirect' => env('GOOGLE_CALLBACK','http://properos.com/auth/google/callback')
        
    ]
```
**Added on config/app.php**
```php
    Laravel\Socialite\SocialiteServiceProvider::class,
```

**Modify config/database.php**
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
        'engine' => 'Innodb',
    ],
```

**Register middleware app/Http/Kernel.php**
```php
    'role' => \Properos\Users\Middlewares\RoleMiddleware::class,
    'permission' => \Properos\Users\Middlewares\PermissionMiddleware::class,
    'role_or_permission' => \Properos\Users\Middlewares\RoleOrPermissionMiddleware::class,
```

**Required moment.js**
npm install moment 

**Run**
    composer dump
    php artisan vendor:publish 
    Note: Publish resources and all files you need to modifie.

**Add on webpack.mix.js**
.js('resources/assets/js/be/modules/users/js/user.js', 'public/be/js/modules/user.js')

**Add on resources/assets/bootstrap.js if not exist**
```js
    import Helpers from './misc/helpers'

    window.moment = require('moment')
    window.Vue = require('vue');
    window.Helpers = Helpers;
```

**config/properos_users.php file**
Set the middleware for the routes.

**How to use a Model**
\Properos\Users\Models\Model-Name

**Modify config/auth.php**
```php
    ...
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users'
        ],
    ],
    ...
```
```php
    ...
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \Properos\Users\Models\User::class,
        ],
    ],
    ...
``` 

**Run migrations**
php artisan migrate
    create  users table
            user_addresses table
            user_profiles table
    modify  roles table

**Add seeder on database/seeds/DatabaseSeeder.php**
```php
    Set all roles on RolesPermissionsTableSeeder and users on UsersTableSeeder
    $this->call(RolesPermissionsTableSeeder::class);
    $this->call(UsersTableSeeder::class);
```
Run composer dump-autoload
php artisan db:seed
npm run watch

  
**Add on routes/web.php**
```php
    Route::get('/admin/dashboard', function(){
        return view('be.index');
    })->middleware(['auth', 'role:admin']);
```

**Add .env**

########################################################################
# LARAVEL MIX
########################################################################

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

MIX_HOST="${DOMAIN}"
MIX_API_BASE_URL=
MIX_CDN_URL=

MIX_ADMIN_WEB_PREFIX='/admin'
MIX_ADMIN_WEB_API_PREFIX='/api/admin'




