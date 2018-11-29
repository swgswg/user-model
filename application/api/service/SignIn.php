<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 18:39
 */

namespace app\api\service;


use app\api\model\User as UserModel;

class SignIn
{
    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @param $field
     * @param $user_name
     * @param $user_pass
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function signIn($field, $user_name, $user_pass)
    {
        // 用户登录
        $user = UserModel::where("$field",'=',$user_name)
            ->where('user_pass', '=', $user_pass)
            ->where('user_status', '=', 1)
            ->find();

        // 更新登录信息
        $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
        $user->last_login_time = time();
        $user->login_count	= ['inc', 1];
        $user->save();

        return $user;
    }


    /**
     *  用户注册
     * @param $params
     * @return UserModel
     */
    public static function signUp($params)
    {
        // 如果没有头像, 给一个默认头像
        if(!array_key_exists('user_photo', $params)){
            $params['user_photo'] = 'aaa.png';
        }
        $params['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
        $params['last_login_time'] = time();
        $data = UserModel::create($params);
        return $data;
    }

}