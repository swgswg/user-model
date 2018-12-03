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

    public $hidden = ['update', 'delete_time'];

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

}