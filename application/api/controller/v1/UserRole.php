<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 11:40
 */

namespace app\api\controller\v1;


use app\api\model\UserRole as UserRoleModel;

class UserRole extends BaseController
{
    // 批量添加用户角色表数据
    public function addBatchUserRoles($user_id, $role_ids)
    {
        $user = new UserRoleModel();
//        $list = [
//            ['user_id'=>1,'role_id'=>1],
//            ['user_id'=>1,'role_id'=>1]
//        ];
        $list = [];
        foreach ($role_ids as $k=>$v){
            $list[] = [
                'user_id'=>$user_id,
                'role_id'=>$v,
            ];
        }
        $res = $user->saveAll($list);
        if($res->isEmpty()){
            return [];
        } else {
            return $res;
        }
    }


    // 单条添加用户角色数据
    public function addOneUserRole($user_id, $role_id, $end_time = null)
    {
        $data = [
            'user_id'=>$user_id,
            'role_id'=>$role_id,
        ];
        if($end_time){
            $end_time = strtotime($end_time);
            $data['end_time'] = $end_time;
        }
        $res = UserRole::create($data);
        if($res){
           return $res;
        } else {
            return [];
        }
    }
}