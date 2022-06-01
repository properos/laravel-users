<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Loggeable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'label', 'description', 'url'];
		 							
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [''];


    public function assignPermission($permission) {
        if (is_string($permission)) {
            $permission = Permission::where('name',$permission)->first();
            if (! $permission) {
                return false;
            }
        }

        if (is_int($permission)) {
            $permission = Permission::where('id', $permission)->first();
            if (! $permission) {
                return false;
            }
        }

        if (! $permission instanceof Permission) {
            return false;
        }

        return $this->permissions()->syncWithoutDetaching($permission);
    }

    public function removePermission($permission) {
        if (is_string($permission)) {
            $permission = Permission::where('name',$permission)->first();
            if (! $permission) {
                return false;
            }
        }

        if (is_int($permission)) {
            $permission = Permission::where('id', $permission)->first();
            if (! $permission) {
                return false;
            }
        }

        if (! $permission instanceof Permission) {
            return false;
        }

        return $this->permissions()->detach($permission);
    }
   
    
    /**
     * An role has many users.
     */
    public function users() {
        return $this->morphedByMany(User::class, 'users', 'user_role', 'role_id', 'user_id')
                ->withPivot(['restrictable_type', 'restrictable_id', 'assigned_at', 'removed_at']);
    }
    
    /**
     * An role has many permissions.
     */
    public function permissions() {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    
    public static function findByName(string $name): Role
    {
        return static::where('name', $name)->first();
    }

    public static function findById(int $id): Role
    {
        return static::where('id', $id)->first();
    }

}
