<?php

namespace Properos\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {       
        // $this->loadRoutesFrom(__DIR__.'/user-routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        if(app()->version() < "5.8"){
            $this->publishes([
                __DIR__.'/properos_users.php' => config_path('properos_users.php'),
                __DIR__.'/resources/assets' => resource_path('assets/js/be/modules/users'),
                __DIR__.'/resources/views/index.blade.php' => resource_path('views/be/users/index.blade.php'),
                __DIR__.'/resources/views/layouts/menu' => resource_path('views/be/layouts/menu'),
                __DIR__.'/user-routes.php' => base_path('routes/user/user-routes.php'),
            ], 'users');
        }else{
            $this->publishes([
                __DIR__.'/properos_users.php' => config_path('properos_users.php'),
                __DIR__.'/resources/assets' => resource_path('js/be/modules/users'),
                __DIR__.'/resources/views/index.blade.php' => resource_path('views/be/users/index.blade.php'),
                __DIR__.'/resources/views/layouts/menu' => resource_path('views/be/layouts/menu'),
                __DIR__.'/user-routes.php' => base_path('routes/user/user-routes.php'),
            ], 'users');
        }

        $this->publishes([
            __DIR__.'/Traits' => base_path('app/Traits/User'),
        ], 'users-traits');

        $this->publishes([
            __DIR__.'/Models' => base_path('app/Models/User'),
        ], 'users-models');

        $this->publishes([
            __DIR__.'/Classes' => base_path('app/Classes/User'),
        ], 'users-classes');

        $this->publishes([
            __DIR__.'/seeds' => base_path('database/seeds'),
        ], 'users-seeds');

        $this->publishes([
            __DIR__.'/Controllers' => app_path('Http/Controllers/Users'),
        ], 'users-controllers');

        $this->registerRoleBladeDirectives();
        $this->registerPermissionBladeDirectives();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Properos\Users\Controllers\UserController');
    }

    private function registerRoleBladeDirectives(){
        Blade::if('role', function ($role) {
            return (Auth::check() && Auth::user()->hasRole($role));
        });

        Blade::if('hasRole', function ($role) {
            return (Auth::check() && Auth::user()->hasRole($role));
        });

        Blade::if('hasAnyRole', function ($role) {
            return (Auth::check() && Auth::user()->hasAnyRole($role));
        });

        Blade::if('hasRoleOn', function ($expression) {
            @list($role, $restrictable_type, $restrictable_id) = explode(',', $expression);
            
            $role = ($role)?trim($role):NULL;
            $restrictable_type = ($restrictable_type)?trim($restrictable_type):NULL;
            $restrictable_id = ($restrictable_id)?trim($restrictable_id):0;

            return (Auth::check() && Auth::user()->hasRoleOn($role, $restrictable_type, $restrictable_id));
        });

        Blade::if('hasRoleOnList', function ($expression) {
            @list($role, $restrictable_type) = explode(',', $expression);
            
            $role = ($role)?trim($role):NULL;
            $restrictable_type = ($restrictable_type)?trim($restrictable_type):NULL;

            return (Auth::check() && Auth::user()->hasRoleOn($role, $restrictable_type));
        });

        Blade::if('hasAllRoles', function ($role) {
            return (Auth::check()&& Auth::user()->hasAllRoles($role));
        });
    }

    private function registerPermissionBladeDirectives(){
        Blade::if('permission', function ($permission) {
            return (Auth::check()&& Auth::user()->hasPermission($permission));
        });

        Blade::if('hasPermission', function ($permission) {
            return (Auth::check()&& Auth::user()->hasPermission($permission));
        });

        Blade::if('hasAnyPermission', function ($permission) {
            return (Auth::check()&& Auth::user()->hasAnyPermission($permission));
        });

        Blade::if('hasPermissionOn', function ($expression) {
            @list($permission, $restrictable_type, $restrictable_id) = explode(',', $expression);
            
            $permission = ($permission)?trim($permission):NULL;
            $restrictable_type = ($restrictable_type)?trim($restrictable_type):NULL;
            $restrictable_id = ($restrictable_id)?trim($restrictable_id):0;

            return (Auth::check() && Auth::user()->hasPermissionOn($permission, $restrictable_type, $restrictable_id));
        });

        Blade::if('hasPermissionOnList', function ($expression) {
            @list($permission, $restrictable_type) = explode(',', $expression);
            
            $permission = ($permission)?trim($permission):NULL;
            $restrictable_type = ($restrictable_type)?trim($restrictable_type):NULL;

            return (Auth::check() && Auth::user()->hasPermissionOn($permission, $restrictable_type));
        });

        Blade::if('hasAllPermissions', function ($permission) {
            return (Auth::check() && Auth::user()->hasAllPermissions($permission));
        });
    }
}
