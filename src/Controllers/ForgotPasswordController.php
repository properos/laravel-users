<?php

namespace Properos\Users\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Properos\Base\Classes\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Properos\Base\Controllers\ApiController;
use Properos\Base\Classes\Api;

class ForgotPasswordController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('themes.'.Theme::get().'.auth.passwords.email')->with(['theme'=>Theme::get()]);
    }

    // public function sendResetLinkEmail(Request $request)
    // {
    //     $this->validateEmail($request);
        
    //     // We will send the password reset link to this user. Once we have attempted
    //     // to send the link, we will examine the response then see the message we
    //     // need to show to the user. Finally, we'll send out a proper response.
    //     $response = $this->broker()->sendResetLink(
    //         $request->only('email')
    //     );

    //     return ($response == Password::RESET_LINK_SENT)
    //             ? Api::success('Sent reset password link!',[]) 
    //             : Api::error('001','Fail sending reset password link',[]);
    // }
}
