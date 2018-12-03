<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 18:39
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\UserException;
use think\facade\Request;
use app\api\model\User as UserModel;
use app\lib\exception\ParameterException;

class SignIn
{
    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws ParameterException
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function signIn()
    {
        $telReg = '/^1[3456789]\d{9}$/';
        $emailReg = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        $string = '/^\w{4,20}$/';
        $user_name = Request::post('user_name');
        $user_pass = Request::post('user_pass');

        if(preg_match($telReg,$user_name)){
            // 手机号登录
            $data = self::userSignIn('user_mobile', $user_name, $user_pass);
        } else if(preg_match($emailReg,$user_name)) {
            // 邮箱登录
            $data = self::userSignIn('user_email', $user_name, $user_pass);
        } else if(preg_match($string,$user_name)){
            // 用户名登录
            $data = self::userSignIn('user_name', $user_name, $user_pass);
        } else {
            throw new ParameterException();
        }
        if(!$data){
            // {"message":"用户不存在","state":0,"error_code":20000,"request_url":"http:\/\/user.com\/login"}
            throw new UserException();
        } else {
            $data = $data->toArray();
        }
//        $data['scope'] = ScopeEnum::User;
        $token = self::getToken($data['id']);
        unset($data['id']);
        $data['token'] = $token;
        return $data;
    }


    /**
     *  获取用户的唯一token
     * @param $user_id
     * @return string
     */
    private static function getToken($user_id)
    {
        $ut = new UserToken($user_id);
        $token = $ut -> get();
        return $token;
    }


    /**
     * @param $field      登录字段(user_mobile/user_email/user_name)
     * @param $user_name  登录名
     * @param $user_pass  密码
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function userSignIn($field, $user_name, $user_pass)
    {
        // 用户登录
        $user = UserModel::where($field,'=',$user_name)
            ->where('user_pass', '=', $user_pass)
            ->where('user_status', '=', 1)
            ->find();
        if(!$user){
            throw new UserException();
        }

        // 更新登录信息
        $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
        $user->last_login_time = time();
        $user->login_count	= ['inc', 1];
        $res = $user->save();
        if(!$res){
            throw new UserException([
                'message'=>'更新登录信息失败',
                'errorCode' => 20001
            ]);
        }
        return $user;
    }

}