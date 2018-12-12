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

    // 用户角色 多对多
    public function roles()
    {
        return $this->belongsToMany('Role', '\\app\\api\\model\\AdminRole','role_id', 'admin_id');
    }

    /**
     *  获取管理员的角色和权限
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOneAuths($id)
    {
        $auths = self::with(['roles', 'roles.auths'])
            ->find($id);
        $auths = $auths['roles']->visible(['auths'=>['auth_route', 'auth_status']])->toArray();
        return $auths;
    }
}