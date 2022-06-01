<?php

namespace Properos\Users\Classes;

use Properos\Base\Classes\Base;
use Properos\Base\Exceptions\ApiException;
use Properos\Users\Models\UserActivityLog;

class CUserActivityLog extends Base
{
	function __construct()
	{
		parent::__construct(UserActivityLog::class, 'User Activity Log');     
	}

	public function init_fillable()
    {
        foreach ($this->model->getFillable() as $key) {
            $this->fillable[$key] = null;
        }
    }

    public function create($data){
        $rules = [
            'user_id' => 'required|min:0',
            'email' => 'nullable|string|max:100',
            'name' => 'required|string|max:191',
            'description' => 'nullable',
            'activity_type' => 'required',
            'ip_address' => 'nullable|string|max:100'
        ];

        $validator = \Validator::make($data, $rules);

        if($validator->passes()){
            $this->createModel($data);
        }else{
            throw new ApiException($validator->errors(), '007', $data);
        }
    }

    public static function log($data){
        $rules = [
            'user_id' => 'nullable|min:0',
            'email' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:191',
            'description' => 'nullable',
            'activity_type' => 'required',
            'activity_id' => 'required',
            'ip_address' => 'nullable|string|max:100'
        ];

        $validator = \Validator::make($data, $rules);

        if($validator->passes()){
            (new CUserActivityLog())->createModel($data);
        }else{
            throw new ApiException($validator->errors(), '007', $data);
        }
    }

}