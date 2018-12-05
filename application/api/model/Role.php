<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:52
 */

namespace app\api\model;


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
    protected function getOrderGroupAttr($value)
    {
        if($value < 100){
            return '用户等级';
        } else {
            return '管理员等级';
        }
    }

    // 查询范围 查询状态为1的
    public function scopeUserStatus($query)
    {
        // 继承 BaseModel
        return $this->scopeStatus($query, 'user_status');
    }

    // 角色权限 多对多
    public function auths()
    {
        return $this->belongsToMany('Auth', 'role_auth_rel','auth_id', 'role_id');
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
        $detail = self::with(['auths'])->find($id);
    }

}