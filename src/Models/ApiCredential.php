<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    use Loggeable;
    
    protected $fillable = [
        'user_id','api','name','data'
    ];

    protected $casts = ['data' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
