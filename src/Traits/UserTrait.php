<?php

namespace Properos\Users\Traits;


use Illuminate\Support\Facades\Cache;


trait UserTrait{

    use UserRoleTrait;
    use UserPermissionTrait;

    public $_home;
    public static $key = 'user_details';

    /**
     * Storing a $key in the Cache
     *
     * @param Mixed $value
     * @param String $key (Default 'user_details')
     * @param Integer $time minutes (Default '2880')
     * @return
     */
    public function setCache($value = null, $time = 2880) {
        $key = $this->key . '_' . $this->id;
        if(is_string($value)){
            $_value = @json_decode($value);
            if(json_last_error() === JSON_ERROR_NONE){
                $value = json_encode($value);
            }
        }else{
            $value = json_encode($value);
        }
        if (Cache::has($key)) {
            Cache::put($key, $value, $time);
        } else {
            Cache::add($key, $value, $time);
        }
    }

    /**
     * Get from a $key storing in the Cache
     *
     * @return Array If exist then array data else false
     */
    public function getCache() {
        $key = $this->key . '_' . $this->id;
        if (Cache::has($key)) {
            $value = Cache::get($key);
            $_value = @json_decode($value, true);
            if(json_last_error() === JSON_ERROR_NONE){
                $value = $_value;
            }
            return $value;
        }else{
            $user = $this->getDetails();
            if(count($user) > 0){
                $this->setCache($user);
                return $user;
            }
        }
        return [];
    }

    /**
     * Removing a $key from the cache
     *
     * @param String $key (Default 'user_details')
     * @return
     */
    public function removeCache() {
        $key = $this->key . '_' .$this->id;
        Cache::forget($key);
    }

    public function refreshUserCache(){
        $this->setCache($this->getDetails());
    }

    /**
     * This method return all user information
     *
     * @author RAHG
     *
     * @param  String  $user_id
     * @param  Array  $field Field of the table would you like take;
     * @return Array
     */
    public function getDetailsById($user_id) {
        return $this->getDetails([['id',$user_id]]); 
    }

    /**
     * This method return all user information
     *
     * @author RAHG
     *
     * @param  Array  $where
     * @return Array
     */
    public function getDetails($where = null) {
        if($where){
            $mUser = self::where($where);
        } else{
            $mUser = self::where('id', $this->id);
        }
        $user = head($mUser->take(1)->get()->toArray());
        if (!$user) {
            return [];
        }
        $user['roles'] = [];
        $user['permissions'] = [];
        if(method_exists($this, 'getRolesFromSQLByUserID')){
            $user['roles'] = $this->getRolesFromSQLByUserID($user['id']);
        }
        if(method_exists($this, 'getPermissionsFromSQLByUserID')){
            $user['permissions'] = $this->getPermissionsFromSQLByUserID($user['id']);
        }
        return $user;
    }
}