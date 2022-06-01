<?php

namespace Properos\Users\Classes;

use Laravel\Socialite\Contracts\User as ProviderUser;
use Properos\Base\Classes\Helper;
use Properos\Users\Models\User;
use Properos\Users\Models\ApiCredential;

class CSocialAccounts
{
	public function GetOrCreateUser(ProviderUser $provider_user, $provider)
    {
        $credential = ApiCredential::where([['api', $provider],['data->provider_user_id',$provider_user->getId()]])->first();
        if ($credential) {
            return User::where('id',$credential->user_id)->first();
        } else {
            $credential = new ApiCredential([
                'user_id' => 0,
                'api' => $provider,
                'name' => $provider_user->getEmail(),
                'data' => [
                    'provider_user_id' => $provider_user->getId(),
                    'user_token' => $provider_user->token,
                    'avatar' => $provider_user->avatar
                ]   
            ]);
            $user = User::where('email',$provider_user->getEmail())->first();
            if (!$user) {
                $name = Helper::splitFullname($provider_user->getName());
                $user = User::create([
                        'email' => $provider_user->getEmail(),
                        'firstname' => $name['first_name'],
                        'lastname' => $name['last_name'],
                        'status' => 'active',
                        'password' => \bcrypt(Helper::generate_random_string(10)),
                ]);
                // dispatch(new \App\Jobs\WelcomeJob($user));
                $this->defaultRoleAssignment($user);
            }

            $credential->user_id = $user->id;
            $credential->save();
        }
        // \Cookie::queue(\Cookie::forget('code'));
        return $user;
    }

    public function defaultRoleAssignment($user)
    {
        $user->assignRole(config('properos_users.socialite.default.role'));
    }
}