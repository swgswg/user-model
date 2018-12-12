<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:52
 */

namespace app\api\model;


use app\lib\exception\RoleException;
use think\model\concern\SoftDelete;

class Role extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'roles';
    protected $pk = 'id';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    protected $hidden = ['update_time', 'delete_time'];

    // 读取器
    protected function getRoleGroupAttr($value)
    {
        if($value < 100){
            return '用户组';
        } else {
            return '管理员组';
        }
    }

    public static function init()
    {
        self::event('before_insert', function ($role) {
            $auth = self::roleNameExist($role->role_name);
            if($auth){
                throw new RoleException([
                    'message'=>'角色已存在',
                    'errorCode'=> 30007
                ]);
            } else {
                return true;
            }
        });
    }


    // 查询范围 查询状态为1的
    public function scopeUserStatus($query)
    {
        // 继承 BaseModel
        $this->scopeStatus($query, 'user_status');
    }

    // 角色权限 多对多
    public function auths()
    {
        return $this->belongsToMany('Auth', '\\app\\api\\model\\RoleAuth','auth_id', 'role_id');
    }

    // 获取所有角色 条件/分页
    public static function allRoles($wheres)
    {
//        $list = self::whereList($wheres);
//        $pageData = self::where($list['where'])
//            ->order('create_time', 'desc')
//            ->order('role_order', 'desc')
//            ->paginate($list['pageSize'], false, ['page'=>$list['page']]);
//        return $pageData;
        $fields = [
            'role_name'=>['role_name', 'like', ''],
            'role_group'=>['role_group', '=', ''],
            'role_status'=>['role_status', '=', ''],
        ];
        return self::paging($wheres, $fields);
    }


    // 角色详情
    public static function roleDetail($id)
    {
        $detail = self::with(['auths'=>function($query) {
            $query->scope('auth_status')
                ->order('id', 'asc');
        }])->find($id);
        return $detail;
    }

    // 角色名称是否存在
    public static function roleNameExist($role_name)
    {
        $role = self::where('role_name', '=', $role_name)->find();
        return $role;
    }

    // 获取所有的角色
    public static function allRolesByWhere($where)
    {
        $roles = self::where($where)
            ->order('create_time', 'desc')
            ->select();
        if($roles->isEmpty()){
            $data = [];
        } else {
            $data = $roles->toArray();
        }
        return $data;
    }
}