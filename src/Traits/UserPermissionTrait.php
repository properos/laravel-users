<?php

namespace Properos\Users\Traits;

use Properos\Base\Classes\Helper;
use Properos\Users\Models\Permission;
use Properos\Users\Models\UserPermission;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait UserPermissionTrait
{

    public $_home;
    public $_permissions;

    public function getPermissionsFromSQLByUserID($id)
    {
        $permissions = [];
        $sql = "SELECT `user_permission`.`permission_id`, `user_permission`.`restrictable_type`, GROUP_CONCAT(`user_permission`.`restrictable_id`) AS 'restrictables', ANY_VALUE(`permissions`.`name`) AS 'name' FROM `user_permission` 
                    LEFT JOIN `permissions` on `user_permission`.`permission_id` = `permissions`.`id`
                WHERE `user_id` = ? AND `user_permission`.`removed_at`  IS NULL
                GROUP By `permission_id`, `user_permission`.`restrictable_type`
        ";
        $_permissions = DB::select($sql, [$id]);

        foreach ($_permissions as $permission) {
            if (!isset($permissions[$permission->name])) {
                $permissions[$permission->name] = [];
            }
            if (isset($permission->restrictable_type)) {
                $permissions[$permission->name][$permission->restrictable_type] = explode(',', Helper::getValue($permission, 'restrictables', ''));
            }
        }

        $sql = "SELECT `user_role`.`role_id`, `user_role`.`restrictable_type`, `user_role`.`restrictable_id`, ANY_VALUE(`roles`.`name`) AS 'role_name', 
                        `permissions`.`id` as 'permission_id', `permissions`.`name` as 'permission_name' FROM `user_role` 
                    LEFT JOIN `roles` on `user_role`.`role_id` = `roles`.`id`
                    LEFT JOIN `role_permission` on `user_role`.`role_id` = `role_permission`.`role_id`
                    LEFT JOIN `permissions` on `role_permission`.`permission_id` = `permissions`.`id`
                WHERE `user_role`.`user_id` = ? AND `user_role`.`removed_at` IS NULL AND `permissions`.`name` IS NOT NULL
                GROUP BY `user_role`.`role_id`, `user_role`.`restrictable_type`, `user_role`.`restrictable_id`, `permissions`.`id`
        ";
        $_role_permissions = DB::select($sql, [$id]);
        foreach ($_role_permissions as $permission) {
            if (!isset($permissions[$permission->permission_name])) {
                $permissions[$permission->permission_name] = [];
            }
            if (isset($permission->restrictable_type)){
                if(!isset($permissions[$permission->permission_name][$permission->restrictable_type])){
                    $permissions[$permission->permission_name][$permission->restrictable_type] = [$permission->restrictable_id . ''];
                } else {
                    if(!in_array($permission->restrictable_id, $permissions[$permission->permission_name][$permission->restrictable_type])) {
                        $permissions[$permission->permission_name][$permission->restrictable_type][] = $permission->restrictable_id . '';
                    }
                }
            }
        }
        


        return $permissions;
    }

    public function getPermissionsFromSQL()
    {
        return $this->getPermissionsFromSQLByUserID($this->id);
    }

    /**
     * This method assign a permission
     *
     * @param Array $options (['restrictable_type', 'restrictable_id'])
     *
     * @return
     */
    public function assignPermission($permission, $options = array())
    {
        $date = date('Y-m-d H:i:s');
        $where = [
            'user_id' => $this->id,
        ];

        $restrictable_types = array_flip(config('properos_users.morphMap'));
        if (is_object($options)) {
            $_opt = [
                'restrictable_type' => Helper::getValue($restrictable_types, get_class($options), null),
                'restrictable_id' => Helper::getValue($options, 'id', null),
            ];
            $options = $_opt;
        }

        if (is_string($permission)) {
            $permission = Permission::where('name',$permission)->first();
            if (! $permission) {
                return false;
            }
            $where['permission_id'] = $permission->id;
        }else if(is_int($permission)){
            $where['permission_id'] = $permission;
        }else if (! $permission instanceof Permission) {
            $where['permission_id'] = $permission->id;
        }else{
            return false;
        }

        if (!empty($options) && Helper::isAssoc($options)) {
            $where['restrictable_type'] = Helper::getValue($options, 'restrictable_type', NULL);
            $where['restrictable_id'] = Helper::getValue($options, 'restrictable_id', NULL);
        }

        $a_permission = UserPermission::firstOrCreate($where, [
            'assigned_at' => $date
        ]);

        $this->refreshUserCache();

        return $a_permission;
    }

    /**
     * This method remove a user to one or more permissions
     *
     * @param Array $options (['restrictable_type', 'restrictable_id'])
     *
     * @return
     */
    public function removePermission($permission, $options = array())
    {
        $date = date('Y-m-d H:i:s');
        $where = [
            ['user_id', $this->id],
        ];

        $restrictable_types = array_flip(config('properos_users.morphMap'));
        if (is_object($options)) {
            $_opt = [
                'restrictable_type' => Helper::getValue($restrictable_types,get_class($options), null),
                'restrictable_id' => Helper::getValue($options, 'id', null),
            ];
            $options = $_opt;
        }

        if (is_string($permission)) {
            $permission = Permission::where('name',$permission)->first();
            if (! $permission) {
                return false;
            }
            $where[] = ['permission_id', $permission->id];
        }else if(is_int($permission)){
            $where[] = ['permission_id', $permission];
        }else if (! $permission instanceof Permission) {
            $where[] = ['permission_id', $permission->id];
        }else{
            return false;
        }
        if (!empty($options) && Helper::isAssoc($options)) {
            $where[] = ['restrictable_type', Helper::getValue($options, 'restrictable_type', NULL)];
            $where[] = ['restrictable_id', Helper::getValue($options, 'restrictable_id', NULL)];
        }

        $a_permission = UserPermission::where($where)->first();

        $a_permission->removed_at = $date;

        $a_permission->save();

        $this->refreshUserCache();

        return $a_permission;
    }

    /**
     * This method returns if a user have the permission.
     * @author RAHG
     *
     * @param   String  $permission_name Permission Name
     * @return  Boolean
     */
    public function hasPermission($permission_name)
    {
        $user = $this->getCache();

        return isset($user['permissions'][$permission_name]);
    }

    /**
     * This method returns if a user have the permission.
     * @author RAHG
     *
     * @param   String  $permission_name Permission Name
     * @param   String  $restrictable_type Restrictable Name
     * @param   Integer $restrictable_id  Restrictable Id
     * @return  Boolean
     */
    public function hasPermissionOn($permission_name, $restrictable_type = NULL, $restrictable_id = 0)
    {
        $user = $this->getCache();

        if (isset($user['permissions'][$permission_name])) {
            if ($restrictable_type != NULL) {
                if (isset($user['permissions'][$permission_name][$restrictable_type])) {
                    if ($restrictable_id > 0) {
                        return in_array($restrictable_id, $user['permissions'][$permission_name][$restrictable_type]);
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

    public function hasPermissionOnList($permission_name, $restrictable_type = NULL)
    {
        $user = $this->getCache();

        if ($restrictable_type != NULL && isset($user['permissions'][$permission_name])) {
            return isset($user['permissions'][$permission_name][$restrictable_type]);
        }
        return false;
    }

    public function getPermissionOnList($permission_name, $restrictable_type = NULL)
    {
        $user = $this->getCache();

        if ($restrictable_type != NULL && isset($user['permissions'][$permission_name]) && isset($user['permissions'][$permission_name][$restrictable_type])) {
            return $user['permissions'][$permission_name][$restrictable_type];
        }else if(isset($user['permissions'][$permission_name])){
            return $user['permissions'][$permission_name];
        }
        return [];
    }

    /**
     * This method returns if a user have any of the permissions.
     * @author RAHG
     *
     * @param   String  $permissions Permissions
     * @return  Boolean
     */
    public function hasAnyPermission($permissions)
    {
        $user = $this->getCache();

        if (is_string($permissions) && false !== strpos($permissions, '|')) {
            $permissions = Helper::convertPipeToArray($permissions);
        }

        if (is_string($permissions)) {
            return isset($user['permissions'][$permissions]);
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (isset($user['permissions'][$permission])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasAllPermissions($permissions): bool
    {
        $user = $this->getCache();

        if (is_string($permissions) && false !== strpos($permissions, '|')) {
            $permissions = Helper::convertPipeToArray($permissions);
        }

        if (is_string($permissions)) {
            return isset($user['permissions'][$permissions]);
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (!(isset($user['permissions'][$permission]))) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    public function scopePermission(Builder $query, $permissions): Builder
    {
        if ($permissions instanceof Collection) {
            $permissions = $permissions->all();
        }

        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }

        $permissions = array_map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission;
            }
            $method = is_numeric($permission) ? 'findById' : 'findByName';

            return Permission::{$method}($permission);
        }, $permissions);

        return $query->whereHas('per$permissions', function ($query) use ($permissions) {
            $query->where(function ($query) use ($permissions) {
                foreach ($permissions as $permission) {
                    $query->orWhere('per$permissions.id', $permission->id);
                }
            });
        });
    }

    public function getCurrentPermissions($permission_name = null, $restrictable_type = NULL)
    {
        $morpMap = config("properos_users.morphMap", []);

        $permissions = $this->permissions($restrictable_type);

        if($permission_name){
            $permissions->where('name', $permission_name);
        }

        $this->current_permissions = [];
        foreach ($permissions->get() as $permission) {
            if (!isset($this->current_permissions[$permission->name])) {
                $this->current_permissions[$permission->name] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'url' => $permission->url,
                    'label' => $permission->label,
                    'restrictables' => []
                ];
            }
            if ($permission->pivot->restrictable_type) {
                $permission->pivot->restrictable = null;
                if($permission->pivot->restrictable_id > 0){
                    $permission->pivot->restrictable = $morpMap[$permission->pivot->restrictable_type]::where('id', $permission->pivot->restrictable_id)->first();
                }
                $this->current_permissions[$permission->name]['restrictables'][] =  $permission->pivot;
            }
        }

        return $this->current_permissions;
    }

    /**
     * An user has many permissions.
     */
    public function permissions($restrictable = [])
    {
        $permissions = $this->belongsToMany(Permission::class, 'user_permission',  'user_id', 'permission_id')->withPivot('restrictable_id', 'restrictable_type', 'assigned_at', 'removed_at')->wherePivot('removed_at', null);

        if (is_object($restrictable)) {
            $restrictable_types = array_flip(config('properos_users.morphMap'));
            $permissions->wherePivot('restrictable_id', Helper::getValue($restrictable, 'id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable_types, get_class($restrictable), null));
            
        }elseif(is_array($restrictable) && count($restrictable) > 0){
            $permissions->wherePivot('restrictable_id', Helper::getValue($restrictable, 'restrictable_id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable, 'restrictable_type', null));
        }

        return $permissions;
    }

    /**
     * An user has many permissions.
     */
    public function permissionsWithTrashed($restrictable = [])
    {
        $permissions = $this->belongsToMany(Permission::class, 'user_permission',  'user_id', 'permission_id')->withPivot('restrictable_id', 'restrictable_type', 'assigned_at', 'removed_at');

        if (is_object($restrictable)) {
            $restrictable_types = array_flip(config('properos_users.morphMap'));
            $permissions->wherePivot('restrictable_id', Helper::getValue($restrictable, 'id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable_types, get_class($restrictable), null));
            
        }elseif(is_array($restrictable) && count($restrictable) > 0){
            $permissions->wherePivot('restrictable_id', Helper::getValue($restrictable, 'restrictable_id', null))
                  ->wherePivot('restrictable_type', Helper::getValue($restrictable, 'restrictable_type', null));
        }

        return $permissions;
    }

}
