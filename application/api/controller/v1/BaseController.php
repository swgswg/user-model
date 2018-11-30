<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 13:20
 */

namespace app\api\controller\v1;


use app\api\service\UserAuth as UserAuthService;
use think\App;
use think\Controller;


class BaseController extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        // 查询用户访问进来的路由是否有权限
        UserAuthService::getOneUserAuth();
    }
}