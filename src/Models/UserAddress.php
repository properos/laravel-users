<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use Loggeable;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'address1', 'address2', 'city', 'zip', 'state', 'country', 'default'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
