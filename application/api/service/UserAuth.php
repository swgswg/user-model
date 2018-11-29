<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/29
 * Time: 13:46
 */

namespace app\api\service;


use think\facade\Request;
use app\api\model\User as UserModel;

class UserAuth
{

    // 查询用户所拥有的权限
    public static function getOneUserAuth()
    {
        // 获取请求的路由 /api/v1/signIn
        $requestRoute = Request::url();
        $user_id = intval(Request::param('user_id'));
        $userRoutes = UserModel::getOneAuths($user_id);

        print_r($userRoutes);
        echo '=============';
    }
}