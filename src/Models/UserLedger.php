<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;

class UserLedger extends Model
{
    use Loggeable;

    protected $fillable = [
        'user_id','type', 'description','debit','credit','sbalance','ebalance', 'status' 
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
