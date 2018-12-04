<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 20:40
 */

namespace app\api\controller\v1;


use think\facade\Request;
use app\api\model\Role as RoleModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\role\EditRoleValidate;
use app\api\validate\role\CreateRoleValidate;
use app\api\validate\role\EditRoleStatusValidate;
use app\lib\exception\RoleException;
use app\api\validate\role\DeleteRoleAuthValidate;
use app\api\validate\SplicingConditionValidate;
use app\api\controller\v1\common\Output;

class Role extends BaseController
{
    /**
     *  获取角色 分页+条件+排序
     * @param page(默认为1)
     * @param pageSize(默认为15)
     * @param role_name
     * @param role_group
     * @param role_status
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function index()
    {
        (new SplicingConditionValidate())->goCheck();
        $roles = RoleModel::allRoles(Request::post());
        if($roles->isEmpty()){
            return Output::out('获取角色', []);
        }
        $roles = $roles->toArray();
        return Output::out('获取角色', $roles);
    }


    /**
     * 增加单个角色
     * @param role_name
     * @param role_desc
     * @param order_group 用户1/管理员100
     * @param role_order
     * @param role_status 1开启/2禁止
     * @param role_auth [1,2,3]
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function create()
    {
        $validate = new CreateRoleValidate();
        $validate->goCheck();
        $newData = $validate->getDataByRule(Request::post());
        $roleAuth = [];
        if(array_key_exists('role_auth', $newData)){
            $roleAuth = json_decode($newData['role_auth']);
            unset($newData['role_auth']);
        }
        $role = RoleModel::create($newData);
        if(!$role->id){
            throw new RoleException([
                'code'=>400,
                'message' => '角色添加失败',
                'errorCode' => 30002
            ]);
        }
        if($roleAuth){
            // 批量增加角色权限
            $this->roleAuth($role, $roleAuth);
        }
        return Output::out('添加角色');
    }

    // 批量增加角色权限
    private function roleAuth($role, $roleAuth)
    {
        $res = $role->auths()->saveAll($roleAuth);
        if(!$res){
            throw new RoleException([
                'code'=>400,
                'message' => '角色添加失败',
                'errorCode' => 30002
            ]);
        }
    }


    /**
     * 获取单个角色的详细信息包括权限
     * @param id
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function show()
    {
        (new IDMustBePositiveInt())->goCheck();
        $detail = RoleModel::roleDetail(Request::post('id'));
        if(!$detail){
            throw new RoleException();
        }
        $detail = $detail->hidden(['auths.pivot'])->toArray();
        return Output::out('获取角色信息', $detail);
    }


    /**
     * 修改角色
     * @param id 角色id(必传且只能为id)
     * @param role_name
     * @param role_desc
     * @param order_group
     * @param role_order
     * @param role_status
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function edit()
    {
        $validate = new EditRoleValidate();
        $validate->goCheck();
        $data = $validate->getDataByRule(Request::post());
        $data = RoleModel::update($data);
        if(!$data){
            throw new RoleException([
                'code' => 400,
                'message' => '角色修改失败',
                'errorCode' => 30001
            ]);
        }
        return Output::out('角色修改');
    }


    /**
     * 修改角色状态
     * @param id
     * @param role_status
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function editStatus()
    {
        (new EditRoleStatusValidate())->goCheck();
        $role = $this->getRole(Request::post('id'));
        $role->role_status = Request::post('role_status');
        $res = $role->save();
        if(!$res){
            throw new RoleException([
                'code'=>400,
                'message' => '修改状态失败',
                'errorCode' => 30003
            ]);
        }
        return Output::out('修改状态');
    }


    /**
     * 软删除角色权限
     * @param id
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function delete()
    {
        (new IDMustBePositiveInt())->goCheck();
        $role = $this->getRole(Request::post('id'));

        // 角色下面有权限不能删除
        $auth = $role -> auths;
        if($auth->isEmpty()){
            $role->delete();
            return Output::out('删除角色');
        } else {
            throw new RoleException([
                'code'=>400,
                'message'=> '角色下面有权限不能删除',
                'errorCode' => 30004
            ]);
        }
    }


    /**
     * 批量删除角色权限
     * @param id
     * @param role_auth  json数组格式
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function deleteRoleAuth()
    {
        (new DeleteRoleAuthValidate())->goCheck();
        $role = $this->getRole(Request::post('id'));
        $role_auth = json_decode(Request::post('role_auth'));
        $res = $role->auths()->detach($role_auth);
        if(!$res){
            throw new RoleException([
                'message' => '角色权限删除失败',
                'errorCode' => 30006
            ]);
        }
    }


    /**
     * 批量增加角色权限
     * @param id
     * @param role_auth json数组格式
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function addRoleAuth()
    {
        (new DeleteRoleAuthValidate())->goCheck();
        $role = $this->getRole(Request::post('id'));
        $role_auth = json_decode(Request::post('role_auth'));
        $this->roleAuth($role, $role_auth);
    }

    // 获取角色
    private function getRole($id)
    {
        $role = RoleModel::get($id);
        if(!$role){
            throw new RoleException([
                'errorCode' => 30005
            ]);
        }
        return $role;
    }

}