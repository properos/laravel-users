<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{

    use Loggeable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_permission';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role_id', 'permission_id'];
		 							
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [''];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
   
    /**
     * An row is owned by a role
     */
    public function role() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    /**
     * An row is owned by a permission
     */
    public function permission() {
        return $this->hasOne(Permission::class, 'id', 'permission_id');
    }

}
