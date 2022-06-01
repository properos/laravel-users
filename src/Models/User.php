<?php

namespace Properos\Users\Models;

use Properos\Users\Traits\Loggeable;
use Properos\Users\Traits\UserTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    use UserTrait;
    use Loggeable;
    use HasApiTokens;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'phone', 'avatar', 'company', 'affiliate_id', 'percent', 'status','last_login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $searchable = [
        'firstname', 'lastname', 'email', 'company', 'phone'
    ];

    public $index_fulltext = false;

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function setAvatarAttribute($value)
    {   
        if(!isset($this->attributes['avatar'])){
            $this->attributes['avatar'] = null;
        }
        if($value instanceof UploadedFile){
            $path = $value->hashName('users/avatars');
            $image = Image::make($value);
            $image->fit(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            });

            if($this->attributes['avatar'] && Storage::disk('public')->exists($this->attributes['avatar'])){
                Storage::disk('public')->delete($this->attributes['avatar']);
            }

            if(Storage::disk('public')->put($path, (string) $image->encode())) {
                $this->attributes['avatar'] = $path;
            }
        }elseif(is_string($value)){
            $this->attributes['avatar'] = $value;
        }
    }

    public function toSearchableArray()
    {
        return array_flip($this->searchable);
    }

    public function getSearchableArray()
    {
        return $this->searchable;
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function affiliate()
    {
        return $this->hasOne(User::class, 'id', 'affiliate_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $email = $this->email;
        $name = $this->firstname.' '.$this->lastname;
        
        \Mail::send('themes.' . \Properos\Base\Classes\Theme::get() . '.emails.password-reset', ['name' => $name,'token' => $token], function ($m) use ($email, $name) {
            $m->from(config('properos_users.mail.email'), env("APP_NAME", "Properos"));
            $m->to($email, $name)->subject(config('properos_users.mail.reset_password_subject'));
        });
    }
    
}
