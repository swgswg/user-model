<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 9:28
 */

namespace app\api\controller\v1;

use app\api\validate\auth\AuthRouteExist;
use app\api\validate\auth\AuthWhereValidate;
use app\api\validate\SplicingConditionValidate;
use think\facade\Request;
use app\api\model\Auth as AuthModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\AuthException;
use app\api\validate\auth\CreateAuthValidate;
use app\api\validate\auth\EditAuthStatusValidate;
use app\api\validate\auth\EditAuthValidate;
use app\api\controller\v1\common\Output;

class Auth extends BaseController
{
    /**
     * 获取所有的auth 条件+分页
     * @param page
     * @param pageSize
     * @param where 条件数组
     * @param order 排序数组
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function index()
    {
        (new SplicingConditionValidate())->goCheck();
        $auths = AuthModel::authCondition(Request::post());
        if($auths->isEmpty()){
           return Output::out('获取所有权限', []);
        }
        $auths = $auths->toArray();
        return Output::out('获取所有权限', $auths);
    }


    /**
     * 获取单个权限
     * @param id
     * @return \think\response\Json
     * @throws AuthException
     * @throws \app\lib\exception\ParameterException
     */
    public function show()
    {
        (new IDMustBePositiveInt())->goCheck();
        $auth = $this->getAuth(Request::post('id'));
        $auth = $auth->toArray();
        return Output::out('获取权限', $auth);
    }


    /**
     * 判断路由是否存在
     * @param auth_route 权限路由
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function routeExist()
    {
        (new AuthRouteExist())->goCheck();
        $auth = AuthModel::routeExist(Request::post('auth_route'));
        if($auth){
            return Output::out('权限路由已经存在', 1);
        } else {
            return Output::out('权限路由不存在', 0);
        }
    }

    /**
     * 添加单个权限
     * @param auth_route
     * @param auth_route_version
     * @param auth_name
     * @param auth_desc
     * @param auth_order
     * @param auth_status
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function create()
    {
        $validate = new CreateAuthValidate();
        $validate->goCheck();
        $newData = $validate->getDataByRule(Request::post());
        $newData['auth_route'] = '/'. trim($newData['auth_route'], '/');

        $auth = AuthModel::create($newData);
        if(!$auth){
            new AuthException([
                'message'=> '添加权限失败',
                'errorCode' => 40001
            ]);
        }
        return Output::out('添加权限');
    }


    /**
     * 修改单个权限
     * @param auth_route
     * @param auth_route_version
     * @param auth_name
     * @param auth_desc
     * @param auth_order
     * @param auth_status
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function edit()
    {
        (new IDMustBePositiveInt())->goCheck();
        $id = Request::post('id');
        $validate = new EditAuthValidate();
        $validate->goCheck();
        $newData = $validate->getDataByRule(Request::post());
        $newData['id'] = $id;
        AuthModel::update($newData);
        return Output::out('修改权限');
    }


    /**
     * 修改状态
     * @param id
     * @param auth_status
     * @return \think\response\Json
     * @throws AuthException
     * @throws \app\lib\exception\ParameterException
     */
    public function editStatus()
    {
        (new EditAuthStatusValidate())->goCheck();
        $auth = $this->getAuth(Request::post('id'));
        $auth->status = Request::post('auth_status');
        $auth->save();
        return Output::out('状态修改');
    }


    /**
     * 删除单个权限 软删除
     * @param id
     * @return \think\response\Json
     * @throws AuthException
     * @throws \app\lib\exception\ParameterException
     */
    public function delete()
    {
        (new IDMustBePositiveInt())->goCheck();
        $auth = $this->getAuth(Request::post('id'));
        $auth->delete();
        return Output::out('删除权限');
    }


    // 根据条件获取所有权限, 不分页
    public function authWhere()
    {
        (new AuthWhereValidate())->goCheck();

        $where = json_decode(Request::post('where'));
        if(empty($where)){
            $auths = AuthModel::authWhere();
        } else {
            $auths = AuthModel::authWhere($where);
        }

        if($auths->isEmpty()){
            $data = [];
        } else {
            $data = $auths->visible(['id', 'auth_name'])->toArray();
        }
        return Output::out('获取条件权限', $data);

    }

    // 获取权限
    private function getAuth($id)
    {
        $auth = AuthModel::get($id);
        if(!$auth){
            throw new AuthException();
        }
        return $auth;
    }
}