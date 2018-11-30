<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 18:39
 */

namespace app\api\service;


use think\facade\Request;
use app\api\model\User;
use app\api\model\User as UserModel;
use app\api\model\User as AdminModel;
use app\api\model\UserRole;
use app\lib\exception\ParameterException;

class SignIn
{
    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @param $model
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function signIn($model)
    {
        $telReg = '/^1[3456789]\d{9}$/';
        $emailReg = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        $user_name = Request::param('user_name');
        $user_pass = Request::param('user_pass');

        if(preg_match($telReg,$user_name)){
            // 手机号登录
            $data = self::userSignIn($model,$model.'_mobile', $user_name, $user_pass);
        } else if(preg_match($emailReg,$user_name)) {
            // 邮箱登录
            $data = self::userSignIn($model,$model.'_email', $user_name, $user_pass);
        } else {
            // 用户名登录
            $data = self::userSignIn($model,$model.'_name', $user_name, $user_pass);
        }
        return $data;
    }


    /**
     *  用户登录
     * @param $model  用户还是管理员(user/admin)
     * @param $field  登录字段(user(admin)_mobile/user(admin)_email/user(admiin)_name)
     * @param $user_name  登录名
     * @param $user_pass  密码
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function userSignIn($model,$field, $user_name, $user_pass)
    {
        if($model == 'user'){
            // 用户登录
            $user = UserModel::where("$field",'=',$user_name)
                ->where('user_pass', '=', $user_pass)
                ->where('user_status', '=', 1)
                ->find();
        } else if($model == 'admin') {
            // 管理员登录
            $user = AdminModel::where("$field",'=',$user_name)
                ->where('admin_pass', '=', $user_pass)
                ->where('admin_status', '=', 1)
                ->find();
        } else {
            // 抛出参数错误异常
            throw new ParameterException();
        }

        // 更新登录信息
        $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
        $user->last_login_time = time();
        $user->login_count	= ['inc', 1];
        $user->save();

        return $user;
    }



    /**
     *  用户注册
     * @param $model  用户还是管理员(user/admin)
     * @param $params 注册数据
     * @return User  返回注册成功用户模型
     * @throws ParameterException
     */
    public static function signUp($model, $params)
    {
        // 如果没有头像, 给一个默认头像
        if(!array_key_exists($model.'_photo', $params)){
            $params[$model.'_photo'] = 'aaa.png';
        }
        $params['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
        $params['last_login_time'] = time();
        if($model == 'user'){
            try{
                $data = UserModel::create($params);
            }catch (\Exception $e){
                throw $e;
            }

        } else if($model == 'admin'){
            try{
                $data = AdminModel::create($params);
            }catch (\Exception $e){
                throw $e;
            }
        } else {
            throw new ParameterException();
        }

        // 给注册用户角色 默认用户普通会员
        try{
            self::ascribedRole($data['id']);
        }catch (\Exception $e){
            throw $e;
        }

        return $data;
    }


    /**
     *  给注册用户角色 默认用户普通会员
     * @param $user_id
     */
    private static function ascribedRole($model,$user_id)
    {

        UserRole::insertOneUserRole($user_id, 1);
    }
}