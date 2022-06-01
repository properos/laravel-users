<?php

namespace Properos\Users\Classes;

use Properos\Base\Classes\Base;
use Properos\Users\Models\User;
use Properos\Base\Classes\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Properos\Base\Exceptions\ApiException;
use Intervention\Image\ImageManagerStatic as Image;

class CUser extends Base
{
    public function __construct()
    {
        parent::__construct(User::class, 'User');
    }

    public function init_fillable()
    {
        foreach ($this->model->getFillable() as $key) {
            switch ($key) {
                case 'percent':
                case 'affiliate_id':
                case 'pending_balance':
                case 'available_balance':
                    $this->fillable[$key] = 0;
                    break;
                case 'status':
                    $this->fillable[$key] = 'active';
                    break;
                default:
                    $this->fillable[$key] = null;
                    break;
            }
        }
    }

    public function create(array $data)
    {
        $rules = [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL'
        ];
        
        $data['password'] = (isset($data['password']))?$data['password']:Helper::generate_random_string(8);
        
        $user = $this->createModel($data, $rules);

        if (isset($data['avatar'])) {
            // $this->saveImage($user, $data['avatar']);
            $user->avatar = $data['avatar'];
            $user->save();
        }

        return $user;
    }

    public function update(array $data)
    {
        $rules = [
            'id' => 'required|integer',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => "required|email|max:255|unique:users,email,{$data['id']},id,deleted_at,NULL"
        ];
        
        $user = $this->updateModel($data, $rules);

        return $user;
    }

    public function delete($id, $where = [])
    {
        return $this->deleteModel($id, $where);
    }

    public function assignRole($data)
    {
        $validation = Validator::make($data, [
            'role_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validation->passes()) {
            $user = $this->mode::find($data['user_id']);

            if (!$user) {
                throw new ApiException($this->title . "  not found.", '006', $data);
            }

            $user->assignRole($data['role_id'], $data);

            return $user->getCurrentRoles();
        }

        throw new ApiException($validation->errors(), '006', $data);
    }

    public function removeRole($data)
    {
        $validation = Validator::make($data, [
            'role_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validation->passes()) {
            $user = $this->mode::find($data['user_id']);

            if (!$user) {
                throw new ApiException($this->title . "  not found.", '006', $data);
            }

            $user->removeRole($data['role_id'], $data);

            return $user->getCurrentRoles();
        }

        throw new ApiException($validation->errors(), '006', $data);
    }

    public function assignPermission($data)
    {
        $validation = Validator::make($data, [
            'permission_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validation->passes()) {
            $user = $this->mode::find($data['user_id']);

            if (!$user) {
                throw new ApiException($this->title . "  not found.", '006', $data);
            }

            return $user->assignPermission($data['permission_id'], $data);
        }

        throw new ApiException($validation->errors(), '006', $data);
    }

    public function removePermission($data)
    {
        $validation = Validator::make($data, [
            'permission_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validation->passes()) {
            $user = $this->mode::find($data['user_id']);

            if (!$user) {
                throw new ApiException($this->title . "  not found.", '006', $data);
            }

            return $user->removePermission($data['permission_id'], $data);
        }

        throw new ApiException($validation->errors(), '006', $data);
    }

    public function changePassword($data)
    {
        $validation = Validator::make($data, [
            'user_id' => 'required|integer',
            'password' => 'required|confirmed|min:8'
        ]);

        if ($validation->passes()) {
            $user = $this->mode::find($data['user_id']);

            if (!$user) {
                throw new ApiException($this->title . "  not found.", '006', $data);
            }

            $user->password = bcrypt($data['password']);

            $user->save();

            return $user;
        }

        throw new ApiException($validation->errors(), '006', $data);
    }

    public function formatting($data)
    {
        if(isset($data['password'])){
            $data['password'] = bcrypt(trim($data['password']));
        }

        return $data;
    }

    public function saveImage(User $user, UploadedFile $file){
        $path = $file->hashName('users/avatars');
        $image = Image::make($file);
        $image->fit(600, 600, function ($constraint) {
            $constraint->aspectRatio();
        });

        if($user->avatar && Storage::disk('public')->exists($user->avatar)){
            Storage::disk('public')->delete($user->avatar);
        }

        if(Storage::disk('public')->put($path, (string) $image->encode())) {
            $user->avatar = $path;
        }

        $user->save();
    }
}
