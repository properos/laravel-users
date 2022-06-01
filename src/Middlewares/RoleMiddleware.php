<?php

namespace Properos\Users\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Properos\Base\Exceptions\ApiException;
use Properos\Users\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            if($request->ajax()){
                throw new ApiException('User is not logged in.', '403');
            }else{
                throw UnauthorizedException::notLoggedIn();
            }
        }

        if (!Auth::user()->hasAnyRole($role)) {
            if($request->ajax()){
                throw new ApiException('User does not have the right roles.', '403');
            }else{
                throw UnauthorizedException::forRoles($role);
            }
        }

        

        return $next($request);
    }
}
