<?php

namespace Properos\Users\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    private $requiredRoles = [];

    private $requiredPermissions = [];

    public static function forRoles(string $roles): self
    {
        $message = 'User does not have the right roles.';

        $exception = new static(403, $message, null, []);
        $exception->requiredRoles = $roles;

        return $exception;
    }

    public static function forPermissions(string $permissions): self
    {
        $message = 'User does not have the right permissions.';

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $permissions;

        return $exception;
    }

    public static function forRolesOrPermissions(string $rolesOrPermissions): self
    {
        $message = 'User does not have any of the necessary access rights.';

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $rolesOrPermissions;

        return $exception;
    }

    public static function notLoggedIn(): self
    {
        return new static(403, 'User is not logged in.', null, []);
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
