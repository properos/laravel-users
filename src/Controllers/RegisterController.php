<?php

namespace Properos\Users\Controllers;

use Properos\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Properos\Base\Classes\Theme;
use Properos\Users\Classes\CUser;
use Properos\Base\Exceptions\ApiException;
use Properos\Base\Classes\Helper;
use Properos\Base\Classes\Api;
use Illuminate\Http\Request;
use Properos\Base\Controllers\ApiController;
use Properos\Users\Classes\CUserActivityLog;
use Properos\Users\Models\Role;

class RegisterController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // dd($data);
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'firstname' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $log = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->firstname . ' ' . $user->lastname,
            'description' => 'User was registered',
            'activity_type' => 'register',
            'ip_address' => $request->ip(),
            'activity_id' => 0
        ];
        (new CUserActivityLog())->create($log);
    }

    public function showRegistrationForm()
    {
        return view('themes.'.Theme::get().'.auth.register')->with(['theme'=>Theme::get()]);
    }

    public function apiRegister(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);

        if ($validator->passes()) {
            $res = $this->apiCreate($data);
            $log = [
                'user_id' => $res['user']['id'],
                'email' => $res['user']['email'],
                'name' => $res['user']['firstname'] . ' ' . $res['user']['lastname'],
                'description' => 'User was registered',
                'activity_type' => 'register',
                'ip_address' => $request->ip(),
                'activity_id' => 0
            ];
            (new CUserActivityLog())->create($log);
            $res['redirect'] = $this->redirectTo;

           //\Auth::guard('web')->login(User::find($res['user']['id']));
            \Auth::loginUsingId($res['user']['id']);
            return $this->respondData('Your account was successfully created.', $res);
        }

        return response()->json(Api::error('002', $validator->errors()));
    }

    protected function apiCreate(array $data, $create_user = true)
    {
        \DB::beginTransaction();
        try {
            if ($create_user) {
                $data['firstname'] = $data['name'];
                $role = Role::where('name', 'contractor')->first();
                if($role){
                    $data['roles'] = [['name' => 'contractor','id'=>$role->id]];
                }
                $user = (new CUser())->create($data);
            } else {
                $user = User::where('email', $data['email'])->first();
            }

            $user->last_login = date('Y-md H:i:s');
            $user->save();
            
            if(config('properos_users.register_role')){
                $user->assignRole(config('properos_users.register_role'));
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            throw new ApiException('Internal Error', '006', $data);
        } catch (ApiException $e) {
            \DB::rollBack();
            return $e->render($data);
        }
        

        \DB::commit();

        $info = [
            'ip' => Helper::ipAddress()
        ];

        $token_data =  $user->createToken(json_encode($info));
        
        $res = [
            'role'  =>   'owner',
            'user' => [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'phone' => $user->phone,
                'address' => null,
                'uuid' => Helper::generate_random_string(16) . Helper::base2base($user->id, 10, 32) 
            ]
        ];

        if ($create_user) {
            $res['auth'] = [
                'access_token' =>  $token_data->plainTextToken,
            ];
        }
        return $res;
    }
}
