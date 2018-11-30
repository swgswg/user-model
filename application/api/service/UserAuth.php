<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/29
 * Time: 13:46
 */

namespace app\api\service;


use app\api\validate\IDMustBePositiveInt;
use think\facade\Request;
use app\api\model\User as UserModel;

class UserAuth
{

    // 查询用户所拥有的权限
    public static function getOneUserAuth()
    {

        // 获取请求的路由 /v1/v1/signIn
        $requestRoute = Request::url();
        // 公共路由
        $publicRoutes = [
//            '/v1/v1/signIn',  // 登录
            '/v1/v1/signUp'   // 注册
        ];
        if(!in_array($requestRoute, $publicRoutes)){
            (new IDMustBePositiveInt())->goCheck();

            $user_id = (Request::param('id'));
            $userRoutes = UserModel::getOneAuths($user_id);
            $routes = [];
            foreach ($userRoutes as $k=>$v){
                foreach ($v['auths'] as $kk=>$vv){
                    if($vv['auth_status'] == 1){
                        $route = $vv['auth_route'];
                        if(!in_array($route, $routes)){
                            array_push($routes,$route);
                        }
                    }
                }
            }
            /* * 把一个人的权限路由放入缓存, 提供后面查询, 不用每次都执行此方法 */

//            print_r($userRoutes);
//            print_r($routes);
//            echo '=============';
        }

    }
}