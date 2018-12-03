<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/2
 * Time: 21:57
 */

namespace app\api\service;


use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use think\Db;

class SignUp
{
    /**
     *  用户注册
     * @param $params
     * @return UserModel
     * @throws UserException
     */
    public static function signUp($params)
    {
        // 开启事务
        Db::startTrans();
        try{
            // 检测用户名/手机号/邮箱是否存在
            foreach($params as $k=>$v){
                self::userNameIsExist([$k=>$v]);
            }

            // 如果没有头像, 给一个默认头像
            if(!array_key_exists('user_photo', $params)){
                $params['user_photo'] = 'aaa.png';
            }
            $params['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
            $params['last_login_time'] = time();

            $user = UserModel::create($params);

            // 给注册用户角色 默认用户普通会员 // 增加关联的中间表数据
            $user->roles()->save(1);

            // 添加一条用户详情记录
            $user->userDetail()->save($user->id);

            // 提交事务
            Db::commit();
        }catch (\Exception $e){
            // 回滚事务
            Db::rollback();
            throw new UserException([
                'message'   => '注册失败',
                'errorCode' => 20002
            ]);
        }
        if(!$user){
            throw new UserException([
                'message'   => '注册失败',
                'errorCode' => 20002
            ]);
        }
        return $user;
    }


    // 检测用户名/手机号/邮箱是否已经被注册
    public static function userNameIsExist($param)
    {
        if(array_key_exists('user_name', $param)){
            $data =  UserModel::userNameIsExist('user_name',$param['user_name']);
            $field = '用户名';
        } else if(array_key_exists('user_mobile',$param)){
            $data =  UserModel::userNameIsExist('user_mobile',$param['user_mobile']);
            $field = '手机号';
        } else if(array_key_exists('user_email',$param)){
            $data =  UserModel::userNameIsExist('user_email',$param['user_email']);
            $field = '邮箱';
        }else {
            return false;
        }
        if($data){
            throw new UserException([
                'message'   => $field.'已存在',
                'errorCode' => 20003
            ]);
        } else {
            return false;
        }
    }

}