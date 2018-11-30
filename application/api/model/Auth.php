<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:53
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class Auth extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'authorities';
    protected $pk = 'id';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public $visible = ['id', 'auth_name', 'auth_route', 'auth_route_version', 'auth_status'];

    // 查询范围 查询状态为1的
    public function scopeUserStatus($query)
    {
        // 继承 BaseModel
        return $this->scopeStatus($query, 'user_status');
    }

}