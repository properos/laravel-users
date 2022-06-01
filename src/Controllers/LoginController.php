<?php

namespace Properos\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Properos\Base\Classes\Theme;
use Properos\Users\Classes\CUserActivityLog;
use Illuminate\Http\Request;
use Properos\Base\Classes\Api;
use Laravel\Passport\Token;
use Properos\Base\Classes\Helper;
// use Properos\Condo\Models\Setting;
use Properos\Users\Models\User;
use Properos\Users\Models\UserAddress;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        // $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('themes.'.Theme::get().'.auth.login')->with(['theme'=>Theme::get()]);
    }

    protected function authenticated($request, $user)
    {
        $user->refreshUserCache();
        $role = $user->getRole();
        $data = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->firstname.' '.$user->lastname,
            'description' => 'Login Action',
            'activity_type' => 'login',
            'ip_address' => Helper::ipAddress(),
            'activity_id' => 0,
        ];
        (new CUserActivityLog())->create($data);

        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // $setting = Setting::get();
        // \Cache::forever('setting', $setting);

        if($role) {
            return redirect()->intended($role->url);
        }
        return redirect()->intended('/account/dashboard');
    }

    public function logout(Request $request)
    {
        if(\Auth::check()){
            $data = [
                'user_id' => \Auth::user()->id,
                'email' => \Auth::user()->email,
                'name' => \Auth::user()->firstname.' '.\Auth::user()->lastname,
                'description' => 'Logout Action',
                'activity_type' => 'logout',
                'ip_address' => $request->ip(),
                'activity_id' => 0,
            ];
            (new CUserActivityLog())->create($data);
    
            $this->guard()->logout();
    
            $request->session()->invalidate();
    
            return $this->loggedOut($request) ?: redirect('/');
        }else{
            return redirect('/');
        }
    }

    public function apiLogin(Request $request)
    {
        $data = $request->all();
        $validator = \Validator::make($data, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(Api::error('006', $validator->errors()), 200);
        }

        $user = User::where('email', $data['email'])->first();
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return response()->json(Api::error('001', ['Too Many Attempts']), 200);
        }

        if (\Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = \Auth::user();
            $roles = $user->getCurrentRoles();
            $scopes = [];
            foreach($roles as $role){
                $scopes[] = $role['name'];
            }
                
            return $this->allowLogin($user, $request, $scopes);
        }

        $log = [
            'user_id' => 0,
            'email' => $data['email'],
            'name' => isset($data['name']) ? $data['name'] : $data['email'],
            'description' => 'Invalid credentials',
            'activity_type' => 'login_error',
            'ip_address' => $request->ip(),
            'activity_id' => 0,
        ];
        (new CUserActivityLog())->create($log);

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return response()->json(Api::error('006', ['Invalid credentials']), 200);
    }

    public function allowLogin($user, $request, $scopes)
    {
        $info = [
            'ip' => Helper::ipAddress()
        ];
                
        $token_data =  $user->createToken(json_encode($info));
        
        $address = UserAddress::where('user_id', $user->id)->where('default',1)->first();
        $res =  [
                    'user' => [
                        'id' => $user->id,
                        'avatar' => $user->avatar,
                        'email' => $user->email,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'phone' => $user->phone,
                        'address' => ($address) ? $address->address1 : null,
                    ],
                    'auth' => [
                        'access_token' =>  $token_data->plainTextToken
                    ],
                    'role' => $scopes,
                ];
        $this->clearLoginAttempts($request);
        
        return response()->json(Api::success('Done!', $res), 200);
    }

    public function apiLogout(Request $request)
    {
        $token = Token::where('id', \Auth::user()->token()->id)->first();
        
        if (is_null($token)) {
            return response()->json(Api::error('006', ['Invalid Query Format.']), 200);
        }

        $token->revoke();

        return response()->json(Api::success('Closed session', []), 200);
    }
}
