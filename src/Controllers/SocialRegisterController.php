<?php
namespace Properos\Users\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Properos\Users\Classes\CSocialAccounts;

class SocialRegisterController extends Controller
{

    public function redirectToProvider($provider)
    {
        $socialite = Socialite::driver($provider);
        $socialite->redirectUrl($this->URL("/auth/$provider/callback"));
        return $socialite->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialite = Socialite::driver($provider);
            $cSocialAccount = new CSocialAccounts();
            $user = $cSocialAccount->GetOrCreateUser($socialite->user(), $provider);
            auth()->login($user);
            return redirect()->intended($this->redirectPath());
        } catch (Exception $e) {
            return redirect('/');
        }
    }

    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }

    private function URL($path) 
    {
       return sprintf( "%s://%s%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME'], $path );
    }
}
