<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/2
 * Time: 21:57
 */

namespace app\api\service;


use app\api\model\Admin as AdminModel;
use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use think\Db;

class SignUp
{
    /**
     *  用户注册
     * @param $params
     * @param $userOrAdmin 1用户/2管理员
     * @return UserModel
     * @throws UserException
     */
    public static function signUp($params, $userOrAdmin = 1)
    {
        // 开启事务
        Db::startTrans();
        try{
            // 检测用户名/手机号/邮箱是否存在
            foreach($params as $k=>$v){
                self::userNameIsExist([$k=>$v], $userOrAdmin);
            }

            // 如果没有头像, 给一个默认头像
            if(!array_key_exists('photo', $params)){
                $params['photo'] = config('program.default_photo');
            }
            $params['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
            $params['last_login_time'] = time();

            if($userOrAdmin === 1){
                $user = UserModel::create($params);

                // 给注册用户角色 默认用户普通会员 // 增加关联的中间表数据
                $user->roles()->save(1);

                // 添加一条用户详情记录
                $user->userDetail()->save($user->id);

            } else if($userOrAdmin === 2){
                $user = AdminModel::create($params);

                // 给注册用户角色 默认用户普通管理员 // 增加关联的中间表数据
                $user->roles()->save(5);

                // 添加一条用户详情记录
//                $user->userDetail()->save($user->id);
            } else {
                new UserException([
                    'message'   => '注册失败',
                    'errorCode' => 20009
                ]);
            }

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
    public static function userNameIsExist($param, $userOrAdmin = 1)
    {
        if(array_key_exists('nickname', $param)){
            if($userOrAdmin === 1){
                $data =  UserModel::userNameIsExist('nickname',$param['nickname']);
            } else if($userOrAdmin === 2) {
                $data =  AdminModel::userNameIsExist('nickname',$param['nickname']);
            }
            $field = '用户名';
        } else if(array_key_exists('mobile',$param)){
            if($userOrAdmin === 1){
                $data =  UserModel::userNameIsExist('mobile',$param['mobile']);
            } else if($userOrAdmin === 2){
                $data =  AdminModel::userNameIsExist('mobile',$param['mobile']);
            }

            $field = '手机号';
        } else if(array_key_exists('email',$param)){
            if($userOrAdmin === 1){
                $data =  UserModel::userNameIsExist('email',$param['email']);
            } else if($userOrAdmin === 2){
                $data =  AdminModel::userNameIsExist('email',$param['email']);
            }
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