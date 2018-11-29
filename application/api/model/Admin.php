<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:54
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class Admin extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'admins';
    protected $pk = 'id';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    // 用户角色 一对多
    public function userRole()
    {
        return $this->hasMany('UserRole', 'user_id','id');
    }
}