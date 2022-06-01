<?php

namespace Properos\Users\Traits;

use Properos\Users\Models\User;
use Properos\Users\Models\UserRole;

trait RestrictableTrait
{
    public function users()
    {
        $restrictable_types = array_flip(config('properos_users.morphMap'));
        return $this->hasManyThrough(User::class, UserRole::class, 'restrictable_id', 'id', 'id', 'user_id')
        ->whereRaw('`user_role`.`restrictable_type` = ?', [$restrictable_types[get_class($this)]]);
    }
}