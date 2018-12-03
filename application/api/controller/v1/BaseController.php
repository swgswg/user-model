<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 13:20
 */

namespace app\api\controller\v1;


use think\App;
use think\Controller;
use app\api\service\UserAuth as UserAuthService;
use app\api\service\Token as TokenServer;


class BaseController extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        // 查询用户访问进来的路由是否有权限
        UserAuthService::getOneUserAuth();
    }


    /**
     *  对用户和管理员的权限控制
     * @throws \app\lib\exception\ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    protected function checkPrimaryScope()
    {
        TokenServer::needPrimaryScope();
    }


    /**
     *  仅对用户的权限
     * @throws \app\lib\exception\ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    protected function checkExclusiveScope()
    {
        TokenServer::needExclusiveScope();
    }


    /**
     *  仅对管理员的权限
     * @throws \app\lib\exception\ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    protected function checkSuperScope()
    {
        TokenServer::needSuperScope();
    }
}