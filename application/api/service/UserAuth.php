<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/29
 * Time: 13:46
 */

namespace app\api\service;


use app\api\model\Admin as AdminModel;
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
//        $pathinfo = Request::pathinfo();
        // static/images/s.jpg
//        $static = substr($pathinfo,0,strpos($pathinfo,'/'));
//        if($static != 'images'){
//            // 公共路由
//        }
        $publicRoutes = [
            '/api/v1/signIn',              // 用户登录
            '/api/v1/signUp',              // 用户注册
            '/api/v1/userNameIsExist',     // 检测用户名是否存在
            '/api/v1/userMobileIsExist',   // 检测用户手机号是否存在
            '/api/v1/userEmailIsExist',    // 检测用户邮箱是否存在

            '/api/v1/token/verify',        // 检测token

            '/api/v1/admin/signIn',              // 管理员登录
            '/api/v1/admin/signUp',              // 管理员注册
            '/api/v1/admin/userNameIsExist',     // 检测管理员名是否存在
            '/api/v1/admin/userMobileIsExist',   // 检测管理员手机号是否存在
            '/api/v1/admin/userEmailIsExist',    // 检测管理员邮箱是否存在

            '/api/v1/file/uploadToOss',    // 文件上传
            '/api/v1/file/uploadToLocal',  // 文件上传
            '/api/v1/excel/excel',  // 文件上传
            '/api/v1/file/DBtoExcel',  // 文件上传
            '/api/v1/file/dataToExcel',  // 文件上传

        ];
        if(!in_array($requestRoute, $publicRoutes)){
            // 获取所有路由
            $routes = Token::getCurrentRoutes();
//            var_dump($routes);
            if(!in_array($requestRoute, $routes)){
                throw new ForbiddenException();
            }
        }

    }


    /**
     *  获取用户的所有权限
     * @param $user_id
     * @param $userOrAdmin 1用户/2管理员
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function OneUserAllAuth($user_id,$userOrAdmin = 1)
    {
        if($userOrAdmin === 1){
            $userRoutes = UserModel::getOneAuths($user_id);

        } else if($userOrAdmin === 2){
            $userRoutes = AdminModel::getOneAuths($user_id);
        }
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