<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/6
 * Time: 11:11
 */

namespace app\api\service;


use app\lib\exception\TokenException;

class UserCryptic
{
    private $uid;
    private $userOrAdmin;

    public function __construct($uid,$userOrAdmin = 1)
    {
        $this->uid = $uid;
        $this->userOrAdmin = $userOrAdmin;
    }

    // 获取Token
    public function get()
    {
        return $this->grantToken($this->uid, $this->userOrAdmin);
    }


    /**
     * 生成Token
     * @param $uid  用户id
     * @param $userOrAdmin 1用户/2管理员
     * @return string
     * @throws \app\lib\exception\CrypticException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function grantToken($uid, $userOrAdmin = 1)
    {
        // 路由
        // 过期时间
        // 生成加密的Token
        $routes = UserAuth::OneUserAllAuth($uid, $userOrAdmin);
        $expire_in = config('program.token_expire_in') + time();
        $token = $this->encryptToken($uid, $routes, $expire_in);
        return $token;
    }


    /**
     * 要生成Token的数组
     * @param $uid 用户id
     * @param $routes 用户的权限路由
     * @param $expire_in 过期时间
     * @return false|string
     */
    private function prepareValue($uid,$routes,$expire_in)
    {
        $data['uid'] = $uid;
        $data['routes'] = $routes;
        $data['expire_in'] = $expire_in;
        return json_encode($data);
    }


    /**
     * 加密Token数据
     * @param $uid  用户id
     * @param $routes  用户的权限路由
     * @param $expire_in 过期时间
     * @return string
     * @throws \app\lib\exception\CrypticException
     */
    private function encryptToken($uid,$routes,$expire_in)
    {
        $data = $this->prepareValue($uid,$routes,$expire_in);
        $encryptToken = (new Cryptic())->encrypt($data);
        return $encryptToken;
    }


    /**
     * 解密Token数据
     * @param $token 解密的Token数据
     * @return mixed
     * @throws TokenException
     */
    public static function decryptToken($token)
    {
        $decryptToken = self::decryption($token);
        if($decryptToken){
            // 判断有没有过期
            if($decryptToken['expire_in'] > time() ){
                return $decryptToken;
            } else {
                throw new TokenException();
            }
        } else {
            throw new TokenException();
        }
    }


    /**
     * 判断Token有没有过期
     * @param $token
     * @return bool
     * @throws TokenException
     */
    public static function tokenExpire($token)
    {
        $decryptToken = self::decryption($token);
        if($decryptToken){
            if(isset($decryptToken['expire_in']) && $decryptToken['expire_in'] > time() ){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    // 解密数据
    private static function decryption($token = '')
    {
        if(!$token){
            throw new TokenException();
        }
        $decryptToken = (new Cryptic())->decrypt($token);
        $decryptToken = json_decode($decryptToken, true);
        return $decryptToken;
    }
}