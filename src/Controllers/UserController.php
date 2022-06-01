<?php

namespace Properos\Users\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Properos\Users\Classes\CUser;
use Properos\Users\Models\Role;
use Properos\Base\Controllers\ApiController;
use Properos\Base\Exceptions\ApiException;
use Properos\Users\Classes\CBase;

class UserController extends ApiController
{
    public function index()
    {
        $roles = Role::all();
        return view('be.users.index')->with(['roles' => $roles]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $cUser = new CUser();

        $options = $cUser->standardize_search($request);

        $list = $cUser->find($options);

        if (isset($options['limit']) && $options['limit'] > 0) {
            $this->setPagination($cUser->get_paginator()->toArray());
        }

        return $this->respondData('User list', $list);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $customer_id
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = 0)
    {
        if ($id > 0) {
            $cUser = new CUser();

            $options = $cUser->standardize_search($request);
            $user = $cUser->findByID($id, $options, true);

            if (!$user) {
                throw new ApiException('User not found.', '009');
            }

            $res = $user->toArray();

            if ($request->has('appends')) {
                $appends = json_decode($request->get('appends', []), true);
                $res = array_merge($res, $cUser->appends($user, $appends));
            }

            return $this->respondData('User info.', $res);
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function showProfile(Request $request)
    {
        return $this->show($request, Auth::user()->id);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        return $this->respondData('User was successfully created.', (new CUser())->create($data));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $customer_id
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = 0)
    {
        if ($id > 0) {
            $data = $request->all();
            $data['id'] = $id;
            return $this->respondData('User was successfully updated.', (new CUser())->update($data));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function updateProfile(Request $request)
    {
        return $this->update($request, Auth::user()->id);
    }

    public function assignRole(Request $request, $id)
    {
        if ($id > 0) {
            $data = $request->all();
            $data['user_id'] = $id;
            return $this->respondData('Role was successfully added.', (new CUser())->assignRole($data));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function removeRole(Request $request, $id)
    {
        if ($id > 0) {
            $data = $request->all();
            $data['user_id'] = $id;
            return $this->respondData('Role was successfully removed.', (new CUser())->removeRole($data));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function assignPermission(Request $request, $id)
    {
        if ($id > 0) {
            $data = $request->all();
            $data['user_id'] = $id;
            return $this->respondData('Permission was successfully added.', (new CUser())->assignPermission($data));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function removePermission(Request $request, $id)
    {
        if ($id > 0) {
            $data = $request->all();
            $data['user_id'] = $id;
            return $this->respondData('Permission was successfully removed.', (new CUser())->removePermission($data));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $customer_id
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = 0)
    {
        if ($id > 0) {
            return $this->respondData('User was successfully deleted.', (new CUser())->delete($id));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function changeMyPassword(Request $request)
    {
        return $this->changePassword($request, Auth::user()->id);
    }

    public function changePassword(Request $request, $id)
    {
        if ($id > 0) {
            $data = $request->all();
            $data['user_id'] = $id;
            return $this->respondData('The password was successfully changed.', (new CUser())->changePassword($data));
        } else {
            throw new ApiException('User not found.', '009');
        }
    }

    public function setUser($user_id)
    {
        Session::put('return_user', Auth::user()->id);
        Auth::loginUsingId($user_id);
        return redirect('/');
    }

    public function returnUser()
    {
        if (Session::has('return_user')) {
            Auth::loginUsingId(Session::pull('return_user', 1));
            return redirect('/admin/users');
        } else {
            return redirect('/');
        }
    }

    public function getRestrictableList(Request $request, $restrictable_type)
    {
        $morph_map = config("properos_users.morphMap", []);
        if (isset($morph_map[$restrictable_type])) {
            $restrictable = new CBase($morph_map[$restrictable_type], $restrictable_type);
            
            $options = $restrictable->standardize_search($request);

            if(!(isset($options['fields']) || isset($options['fields_raw']))){
                $options['fields_raw'] = $restrictable->getFieldsToSelect();
            }

            $list = $restrictable->find($options);

            if (isset($options['limit']) && $options['limit'] > 0) {
                $this->setPagination($restrictable->get_paginator()->toArray());
            }

            return $this->respondData("$restrictable_type list", $list);
        }

        throw new ApiException('Restrictable Type not found.', '009');
    }
}


