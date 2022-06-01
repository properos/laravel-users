<?php

namespace Properos\Users\Traits;

use Properos\Base\Classes\Helper;
use Illuminate\Support\Facades\Auth;
use Properos\Users\Classes\CUserActivityLog;

trait Loggeable
{
    public static function bootLoggeable()
    {

        static::created(function ($model){
            $data = [
                'description' => class_basename($model).' created',
                'activity_type' => class_basename($model),
                'activity_id' => $model->id,
                'ip_address' => Helper::ipAddress()
            ];

            if(Auth::check()){
                $data['user_id'] = Auth::user()->id;
                $data['email'] = Auth::user()->email;
                $data['name'] = Auth::user()->fullname;
            }else{
                $data['user_id'] = 0;
                $data['email'] = 'Unknown';
                $data['name'] = 'Unknown';
            }

            CUserActivityLog::log($data);
        });

        static::updated(function ($model){
            $data = [
                'description' => class_basename($model).' updated',
                'activity_type' => class_basename($model),
                'activity_id' => $model->id,
                'ip_address' => Helper::ipAddress()
            ];
            
            if(Auth::check()){
                $data['user_id'] = Auth::user()->id;
                $data['email'] = Auth::user()->email;
                $data['name'] = Auth::user()->fullname;
            }else{
                $data['user_id'] = 0;
                $data['email'] = 'Unknown';
                $data['name'] = 'unknown';
            }

            CUserActivityLog::log($data);
        });

    }

    public function logDeleted($model){
        $action = 'soft-deleted';
        if($model->forceDeleting === true) {
            $action = 'deleted';
        }
        $data = [
            'description' => class_basename($model).' '. $action,
            'activity_type' => class_basename($model),
            'activity_id' => $model->id,
            'ip_address' => Helper::ipAddress()
        ];
        
        if(Auth::check()){
            $data['user_id'] = Auth::user()->id;
            $data['email'] = Auth::user()->email;
            $data['name'] = Auth::user()->fullname;
        }else{
            $data['user_id'] = 0;
            $data['email'] = 'Unknown';
            $data['name'] = 'unknown';
        }

        CUserActivityLog::log($data);
    }
}