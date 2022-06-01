<?php

namespace Properos\Users\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Properos\Base\Exceptions\ApiException;
use Properos\Users\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
        if (Auth::guest()) {
            if($request->ajax()){
                throw new ApiException('User is not logged in.', '403');
            }else{
                throw UnauthorizedException::notLoggedIn();
            }
        }

        if (!Auth::user()->hasAnyRole($roleOrPermission) && !Auth::user()->hasAnyPermission($roleOrPermission)) {
            if($request->ajax()){
                throw new ApiException('User is not logged in.', '403');
            }else{
                throw UnauthorizedException::forRolesOrPermissions($roleOrPermission);
            }
        }

        return $next($request);
    }
}
