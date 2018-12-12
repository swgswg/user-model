<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 18:39
 */

namespace app\api\service;


use app\api\model\Admin as AdminModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\UserException;
use think\facade\Request;
use app\api\model\User as UserModel;
use app\lib\exception\ParameterException;

class SignIn
{
    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @param $userOrAdmin 1用户/2管理员
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws ParameterException
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function signIn($userOrAdmin = 1)
    {
        $telReg = '/^1[3456789]\d{9}$/';
        $emailReg = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        $string = '/^\w{4,20}$/';
        $nickname = Request::post('nickname');
        $password = Request::post('password');

        if(preg_match($telReg,$nickname)){
            // 手机号登录
            $data = self::userSignIn('mobile', $nickname, $password, $userOrAdmin);
        } else if(preg_match($emailReg,$nickname)) {
            // 邮箱登录
            $data = self::userSignIn('email', $nickname, $password, $userOrAdmin);
        } else if(preg_match($string,$nickname)){
            // 用户名登录
            $data = self::userSignIn('nickname', $nickname, $password, $userOrAdmin);
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
        $token = self::getToken($data['id'], $userOrAdmin);
        unset($data['id']);
        $data['token'] = $token;
        return $data;
    }


    /**
     *  获取用户的唯一token
     * @param $user_id
     * @param $userOrAdmin 1用户/2管理员
     * @return string
     */
    private static function getToken($user_id, $userOrAdmin)
    {
        $ut = new UserToken($user_id, $userOrAdmin);
        $token = $ut -> get();
        return $token;
    }


    /**
     * @param $field      登录字段(user_mobile/user_email/user_name)
     * @param $nickname  登录名
     * @param $password  密码
     * @param $userOrAdmin 1用户/2管理员
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function userSignIn($field, $nickname, $password, $userOrAdmin = 1)
    {
        if($userOrAdmin === 1){
            // 用户登录
            $user = UserModel::where($field,'=',$nickname)
                ->where('password', '=', $password)
                ->visible(['id','nickname', 'photo'])
                ->find();
        } else if($userOrAdmin === 2){
            // 用户登录
            $user = AdminModel::where($field,'=',$nickname)
                ->where('password', '=', $password)
                ->visible(['id','nickname', 'photo'])
                ->find();
        }

        if(!$user){
            throw new UserException();
        }

        // 更新登录信息
        return self::updateLoginInfo($user, $userOrAdmin);
    }


    // 更新登录信息
    private static function updateLoginInfo($user, $userOrAdmin = 1)
    {
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
        if($userOrAdmin === 1){
            if($user->status == 2){
                throw new UserException([
                    'code'=>403,
                    'message' => '用户禁止登陆',
                    'errorCode' => 20005
                ]);
            }
        } else if($userOrAdmin === 2){
            if($user->status == 2){
                throw new UserException([
                    'code'=>403,
                    'message' => '用户禁止登陆',
                    'errorCode' => 20005
                ]);
            }
        }
        return $user;
    }
}