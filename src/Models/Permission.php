<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use Loggeable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'label', 'description'];
		 							
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [''];
   
    
    /**
     * An permission has many users.
     */
    public function users() {
        return $this->belongsToMany(User::class, 'user_permission', 'permission_id', 'user_id')                
                ->withPivot(['restrictable_type', 'restrictable_id', 'assigned_at', 'removed_at']);
    }
    
    /**
     * An permission has many permissions.
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
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
