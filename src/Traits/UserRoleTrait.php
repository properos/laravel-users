<?php

namespace Properos\Users\Traits;

use Properos\Base\Classes\Helper;
use Properos\Users\Models\Role;
use Properos\Users\Models\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait UserRoleTrait
{
    public $_roles;
    public $current_roles = [];

    public function getRolesFromSQLByUserID($id)
    {
        $roles = [];
        $sql = "SELECT `user_role`.`role_id`, `user_role`.`restrictable_type`, GROUP_CONCAT(`user_role`.`restrictable_id`) AS 'restrictables', ANY_VALUE(`roles`.`name`) AS 'name' FROM `user_role` 
                    LEFT JOIN `roles` on `user_role`.`role_id` = `roles`.`id`
                WHERE `user_id` = ? AND `user_role`.`removed_at`  IS NULL
                GROUP BY `role_id`, `user_role`.`restrictable_type`
        ";
        $_roles = DB::select($sql, [$id]);

        foreach ($_roles as $role) {
            if (!isset($roles[$role->name])) {
                $roles[$role->name] = [];
            }
            if (isset($role->restrictable_type)) {
                $roles[$role->name][$role->restrictable_type] = explode(',', Helper::getValue($role, 'restrictables', ''));
            }
        }

        return $roles;
    }

    public function getRolesFromSQL()
    {
        return $this->getRolesFromSQLByUserID($this->id);
    }

    /**
     * This method assign a role
     *
     * @param Array $options (['restrictable_type', 'restrictable_id'])
     *
     * @return
     */
    public function assignRole($role, $options = array())
    {
        $date = date('Y-m-d H:i:s');

        $restrictable_types = array_flip(config('properos_users.morphMap'));
        if (is_object($options)) {
            $_opt = [
                'restrictable_type' => Helper::getValue($restrictable_types, get_class($options), null),
                'restrictable_id' => Helper::getValue($options, 'id', null),
            ];
            $options = $_opt;
        }

        $where = [
            'user_id' => $this->id,
            'removed_at' => NULL,
        ];

        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
            if (!$role) {
                return false;
            }
            $where['role_id'] = $role->id;
        } else if (is_int($role)) {
            $where['role_id'] = $role;
        } else if (!$role instanceof Role) {
            $where['role_id'] = $role->id;
        } else {
            return false;
        }

        if (!empty($options) && Helper::isAssoc($options)) {
            $where['restrictable_type'] = Helper::getValue($options, 'restrictable_type', NULL);
            $where['restrictable_id'] = Helper::getValue($options, 'restrictable_id', NULL);
        }

        $a_role = UserRole::firstOrCreate($where, [
            'assigned_at' => $date
        ]);

        $this->refreshUserCache();

        return $a_role;
    }

    /**
     * This method remove a user to one or more roles
     *
     * @param Array $options (['restrictable_type', 'restrictable_id'])
     *
     * @return
     */
    public function removeRole($role, $options = array())
    {
        $date = date('Y-m-d H:i:s');

        $restrictable_types = array_flip(config('properos_users.morphMap'));
        if (is_object($options)) {
            $_opt = [
                'restrictable_type' => Helper::getValue($restrictable_types, get_class($options), null),
                'restrictable_id' => Helper::getValue($options, 'id', null),
            ];
            $options = $_opt;
        }

        $where = [
            ['user_id', $this->id],
            ['removed_at', null],
        ];

        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
            if (!$role) {
                return false;
            }
            $where[] = ['role_id', $role->id];
        } else if (is_int($role)) {
            $where[] = ['role_id', $role];
        } else if (!$role instanceof Role) {
            $where[] = ['role_id', $role->id];
        } else {
            return false;
        }
        if (!empty($options) && Helper::isAssoc($options)) {
            $where[] = ['restrictable_type', Helper::getValue($options, 'restrictable_type', NULL)];
            $where[] = ['restrictable_id', Helper::getValue($options, 'restrictable_id', NULL)];
        }

        $_role = UserRole::where($where)->first();

        if ($_role) {
            $_role->removed_at = $date;
            $_role->save();
        }

        return $_role;
    }

    /**
     * This method returns if a user have the role.
     * @author RAHG
     *
     * @param   String  $role_name Role Name
     * @return  Boolean
     */
    public function hasRole($roles)
    {
        $user = $this->getCache();

        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = Helper::convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return isset($user['roles'][$roles]);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (isset($user['roles'][$role])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * This method returns if a user have the role.
     * @author RAHG
     *
     * @param   String  $role_name Role Name
     * @return  Boolean
     */
    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }

    /**
     * This method returns if a user have the role.
     * @author RAHG
     *
     * @param   String  $role_name Role Name
     * @param   String  $restrictable_type Restrictable Name
     * @param   Integer $restrictable_id  Restrictable Id
     * @return  Boolean
     */
    public function hasRoleOn($role_name, $restrictable_type = NULL, $restrictable_id = 0)
    {
        $user = $this->getCache();

        if (isset($user['roles'][$role_name])) {
            if ($restrictable_type != NULL) {
                if (isset($user['roles'][$role_name][$restrictable_type])) {
                    if ($restrictable_id > 0) {
                        return in_array($restrictable_id, $user['roles'][$role_name][$restrictable_type]);
                    }
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function hasRoleOnList($role_name, $restrictable_type = NULL)
    {
        $user = $this->getCache();

        if ($restrictable_type != NULL && isset($user['roles'][$role_name])) {
            return isset($user['roles'][$role_name][$restrictable_type]);
        }
        return false;
    }

    public function getRoleOnList($role_name, $restrictable_type = NULL)
    {
        $user = $this->getCache();

        if ($restrictable_type != NULL && isset($user['roles'][$role_name]) && isset($user['roles'][$role_name][$restrictable_type])) {
            return $user['roles'][$role_name][$restrictable_type];
        } elseif (isset($user['roles'][$role_name])) {
            return $user['roles'][$role_name];
        }
        return [];
    }

    public function scopeRole(Builder $query, $roles): Builder
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(function ($role) {
            if ($role instanceof Role) {
                return $role;
            }
            $method = is_numeric($role) ? 'findById' : 'findByName';

            return Role::{$method}($role);
        }, $roles);

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere('roles.id', $role->id);
                }
            });
        });
    }

    public function hasAllRoles($roles): bool
    {
        $user = $this->getCache();

        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = Helper::convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return isset($user['roles'][$roles]);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!(isset($user['roles'][$role]))) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    public function getCurrentRoles($role_name = null, $restrictable_type = NULL)
    {
        $morpMap = config("properos_users.morphMap", []);

        $roles = $this->roles($restrictable_type);

        if($role_name){
            $roles->where('name', $role_name);
        }

        foreach ($roles->get() as $role) {
            if (!isset($this->current_roles[$role->name])) {
                $this->current_roles[$role->name] = [
                    'id' => $role->id,
                    'name' => $role->name,
                    'url' => $role->url,
                    'label' => $role->label,
                    'restrictables' => []
                ];
            }
            if ($role->pivot->restrictable_type) {
                $role->pivot->restrictable = null;
                if ($role->pivot->restrictable_id > 0) {
                    $role->pivot->restrictable = $morpMap[$role->pivot->restrictable_type]::where('id', $role->pivot->restrictable_id)->first();
                }
                $this->current_roles[$role->name]['restrictables'][] =  $role->pivot;
            }
        }

        return $this->current_roles;
    }

    public function getRole()
    {
        return $this->roles()->first();
    }

    /**
     * An user has many roles.
     */
    public function roles($restrictable = [])
    {
        $roles = $this->belongsToMany(Role::class, 'user_role',  'user_id', 'role_id')->withPivot('restrictable_id', 'restrictable_type', 'assigned_at', 'removed_at')->wherePivot('removed_at', null);

        if (is_object($restrictable)) {
            $restrictable_types = array_flip(config('properos_users.morphMap'));
            $roles->wherePivot('restrictable_id', Helper::getValue($restrictable, 'id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable_types, get_class($restrictable), null));
            
        }elseif(is_array($restrictable) && count($restrictable) > 0){
            $roles->wherePivot('restrictable_id', Helper::getValue($restrictable, 'restrictable_id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable, 'restrictable_type', null));
        }

        return $roles;
    }

    /**
     * An user has many roles.
     */
    public function rolesWithTrashed($restrictable = [])
    {
        $roles = $this->belongsToMany(Role::class, 'user_role',  'user_id', 'role_id')->withPivot('restrictable_id', 'restrictable_type', 'assigned_at', 'removed_at');

        if (is_object($restrictable)) {
            $restrictable_types = array_flip(config('properos_users.morphMap'));
            $roles->wherePivot('restrictable_id', Helper::getValue($restrictable, 'id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable_types, get_class($restrictable), null));
            
        }elseif(is_array($restrictable) && count($restrictable) > 0){
            $roles->wherePivot('restrictable_id', Helper::getValue($restrictable, 'restrictable_id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable, 'restrictable_type', null));
        }

        return $roles;
    }
}
