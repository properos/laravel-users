<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{

    use Loggeable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_permission';

    const CREATED_AT = 'assigned_at';
    const UPDATED_AT = NULL;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['permission_id', 'user_id', 'restrictable_type', 'restrictable_id', 'assigned_at', 'removed_at'];
		 							
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [''];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'assigned_at', 'removed_at'
    ];
   
    /**
     * Get all of the owning restrictable models.
     */
    public function restrictable() {
        return $this->morphTo();
    }
    
    /**
     * An row is owned by a permission
     */
    public function permission() {
        return $this->hasOne(Permission::class, 'id', 'permission_id');
    }

    /**
     * An row is owned by a user
     */
    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
