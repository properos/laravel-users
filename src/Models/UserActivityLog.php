<?php

namespace Properos\Users\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{

    protected $fillable = [
        'user_id', 'email', 'name', 'description','activity_type', 'ip_address', 'activity_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
