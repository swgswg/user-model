<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 9:45
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;

class UserToken extends Token
{
    protected $uid;

    function __construct($uid)
    {
        $this->uid = $uid;
    }

    public function get()
    {
        return $this->grantToken($this->uid);
    }


    /**
     *  给uid用户生成令牌
     * @param $uid
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function grantToken($uid)
    {
        $routes = UserAuth::OneUserAllAuth($uid);
        $cachedValue = $this->prepareCachedValue($routes,$uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }


    /**
     *  生成缓存值
     * @param $routes
     * @param $uid
     * @return mixed
     */
    private function prepareCachedValue($routes,$uid)
    {
        $cachedValue['routes'] = $routes;
        $cachedValue['uid'] = $uid;
//        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }


    /**
     *  生成对应的token缓存
     * @param $cachedValue
     * @return string
     */
    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('program.token_expire_in');
        $result = cache($key, $value, $expire_in);

        if (!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

}