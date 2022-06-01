<?php

namespace Properos\Users\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Properos\Users\Exceptions\UnauthorizedException;
use Properos\Base\Exceptions\ApiException;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::guest()) {
            if($request->ajax()){
                throw new ApiException('User is not logged in.', '403');
            }else{
                throw UnauthorizedException::notLoggedIn();
            }        
        }

        if (Auth::user()->hasAnyPermission($permission)) {
            return $next($request);
        }


        if($request->ajax()){
            throw new ApiException('User does not have the right permissions.', '403');
        }else{
            throw UnauthorizedException::forPermissions($permission);
        }        
    }
}
