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
use app\lib\exception\ForbiddenException;

class UserAuth
{

    /**
     *  判断用户权限
     * @throws ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function getOneUserAuth()
    {
        // 获取请求的路由 /v1/v1/signIn
        $requestRoute = Request::url();
        // 公共路由
        $publicRoutes = [
            '/api/v1/signIn',  // 登录
            '/api/v1/signUp'   // 注册
        ];
        if(!in_array($requestRoute, $publicRoutes)){
            // 获取所有路由
            $routes = Token::getCurrentRoutes();
//            print_r($routes);
            if(!in_array($requestRoute, $routes)){
                throw new ForbiddenException();
            }
        }
    }


    /**
     *  获取用户的所有权限
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function OneUserAllAuth($user_id)
    {
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
        return $routes;
    }

}