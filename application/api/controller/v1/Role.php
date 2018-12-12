<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 20:40
 */

namespace app\api\controller\v1;



use app\api\validate\WhereValidate;
use think\Db;
use think\Exception;
use think\facade\Request;
use app\api\model\Role as RoleModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\role\EditRoleValidate;
use app\api\validate\role\CreateRoleValidate;
use app\lib\exception\RoleException;
use app\api\validate\role\DeleteRoleAuthValidate;
use app\api\validate\SplicingConditionValidate;
use app\api\validate\EditStatusValidate;
use app\api\validate\role\RoleNameExistValidate;
use app\api\controller\v1\common\Output;

class Role extends BaseController
{
    /**
     *  获取角色 分页+条件+排序
     * @param page(默认为1)
     * @param pageSize(默认为15)
     * @param where 条件
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
     * 判断角色名称是否存在
     * @param role_name
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function roleNameExist()
    {
        (new RoleNameExistValidate())->goCheck();
        $role = RoleModel::roleNameExist(Request::post('role_name'));
        if($role){
            return Output::out('角色已存在', 1,true);
        } else {
            return Output::out('角色不存在', 0, true);
        }
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
        Db::startTrans();
        try{
            $validate = new CreateRoleValidate();
            $validate->goCheck();
            $newData = $validate->getDataByRule(Request::post());
            $roleAuth = [];
            if(array_key_exists('role_auth', $newData)){
                $roleAuth = json_decode($newData['role_auth']);
                unset($newData['role_auth']);
            }
            $role = RoleModel::create($newData);
            if(!empty($roleAuth)){
                // 批量增加角色权限
                $this->roleAuth($role, $roleAuth);
            }
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            throw new RoleException([
                'message' => '角色添加失败',
                'errorCode' => 30002
            ]);
        }
//        $role = RoleModel::create($newData);
//        if(!$role->id){
//            throw new RoleException([
//                'code'=>400,
//                'message' => '角色添加失败',
//                'errorCode' => 30002
//            ]);
//        }
//        if(!empty($roleAuth)){
//            // 批量增加角色权限
//            $res = $this->roleAuth($role, $roleAuth);
//        }
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
        return $res;
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
        $detailArr = $detail->visible([
            'id',
            'role_name',
            'role_desc',
            'role_group',
            'role_status',
            'create_time',
            'auths.id',
            'auths.auth_name',
        ])->toArray();
        $authIds = [];
        foreach ($detailArr['auths'] as $k => $v){
            array_push($authIds,($v['id']));
        }
        $detailArr['authIds'] = $authIds;
        return Output::out('获取角色信息', $detailArr);
    }


    /**
     * 修改角色
     * @param id 角色id(必传且只能为id)
     * @param role_name
     * @param role_desc
     * @param order_group
     * @param role_order
     * @param role_status
     * @param role_auth 要删除的权限id json数组
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function edit()
    {
        $validate = new EditRoleValidate();
        $validate->goCheck();
        $data = $validate->getDataByRule(Request::post());
        if(array_key_exists('role_auth', $data)){
            $role_auth = json_decode($data['role_auth']);
            unset($data['role_auth']);
        }
        $role = RoleModel::update($data);
        if(!$role){
            throw new RoleException([
                'code' => 400,
                'message' => '角色修改失败',
                'errorCode' => 30001
            ]);
        }
        // 删除中间表数据
        if(!empty($role_auth)){
            $res = $role->auths()->detach([1,2,3]);
            if(!$res){
                throw new RoleException([
                    'message'=>'修改角色权限失败',
                    'errorCode'=> 30009
                ]);
            }
        }
        return Output::out('角色修改');
    }


    /**
     * 修改角色状态
     * @param id
     * @param status
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function editStatus()
    {
        (new EditStatusValidate())->goCheck();
        $role = $this->getRole(Request::post('id'));
        $role->role_status = Request::post('status');
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
     * @return \think\response\Json
     * @throws RoleException
     * @throws \app\lib\exception\ParameterException
     */
    public function addRoleAuth()
    {
        (new DeleteRoleAuthValidate())->goCheck();
        $role = $this->getRole(Request::post('id'));
        $role_auth = json_decode(Request::post('role_auth'));
        $res = $this->roleAuth($role, $role_auth);
        if(!$res){
            throw new RoleException([
                'message'=>'批量增加角色权限失败',
                'errorCode'=> 30008
            ]);
        }
        return Output::out('批量增加角色权限');
    }


    /**
     * 根据条件获取所有角色
     * @param where 条件 json二维数组
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getAllRolesByWhere()
    {
        (new WhereValidate())->goCheck();
        $where = Request::post('where');
        if(!is_array($where)){
            $where = json_decode($where);
        }
        $data = RoleModel::allRolesByWhere($where);
        return Output::out('获取角色',$data );
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