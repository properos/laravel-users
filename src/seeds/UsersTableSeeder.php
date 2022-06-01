<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Properos\Base\Classes\Helper;
use Properos\Users\Models\User;
use Properos\Users\Models\UserProfile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $this->addUser([
            'email' => "admin1@mail.com",
            'roles' => [
                'admin' => []
            ]
        ]);
        $this->addUser([
            'email' => "admin2@mail.com",
            'roles' => [
                'admin' => []
            ],
        ]);
        $this->addUser([
            'email' => "admin3@mail.com",
            'roles' => [
                'admin' => []
            ]
        ]);
        $this->addUser([
            'email' => "customer1@mail.com",
            'roles' => [
                'customer' => []
            ]
        ]);
        $this->addUser([
            'email' => "admin4@mail.com",
            'roles' => [
                'admin' => []
            ]
        ]);
    }

    public function addUser($data){
        $faker = Factory::create();
        
        $where = [
            'email' => $data["email"]
        ];
        
        $user = User::firstOrCreate($where, [
            'firstname' => Helper::getValue($data, 'firstname', $faker->firstName),
            'lastname' => Helper::getValue($data, 'lastname', $faker->lastName),
            'password' => bcrypt(Helper::getValue($data, 'password','test123')),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        UserProfile::firstOrCreate([
            'user_id' => $user->id,
        ],[
            'created_at' => now(),
            'updated_at' => now() 
        ]);

        if(isset($data['roles'])){
            foreach ($data['roles'] as $role => $options) {
                if(count($options) > 0){
                    foreach ($options as $type => $ids) {
                        if(is_string($ids)){
                            $user->assignRole($role, [
                                'restrictable_type' => $type, 
                                'restrictable_id' => $ids,
                            ]);
                        }elseif(is_array($ids)){
                            foreach ($ids as $id) {
                                $user->assignRole($role, [
                                    'restrictable_type' => $type, 
                                    'restrictable_id' => $id,
                                ]);
                            }
                        }
                    }
                }else{
                    $user->assignRole($role);
                }
            }
        }
        
        if(isset($data['permissions'])){
            foreach ($data['permissions'] as $role => $options) {
                if(count($options) > 0){
                    foreach ($options as $type => $ids) {
                        if(is_string($ids)){
                            $user->assignPermission($role, [
                                'restrictable_type' => $type, 
                                'restrictable_id' => $ids,
                            ]);
                        }elseif(is_array($ids)){
                            foreach ($ids as $id) {
                                $user->assignPermission($role, [
                                    'restrictable_type' => $type, 
                                    'restrictable_id' => $id,
                                ]);
                            }
                        }
                    }
                }else{
                    $user->assignPermission($role);
                }
            }
        }

        return $user;
    }

    public function userTruncate(){
        DB::table('users')->truncate();
        DB::table('user_profiles')->truncate();
        DB::table('user_role')->truncate();
    }
}
