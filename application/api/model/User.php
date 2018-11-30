<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:46
 */

namespace app\api\model;

use think\model\concern\SoftDelete;

class User extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'users';
    protected $pk = 'id';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    // 隐藏字段
    // protected $hidden = ['create_time', 'update_time', 'delete_time'];
    // 显示字段
    public $visible = ['id', 'user_name', 'user_photo'];

    // 读取器 get-UserPhoto(数据库字段 驼峰命名法)-Attr(固定写法)
    // 设置完整图片路径
    protected function getUserPhotoAttr($value)
    {
        // 继承 BaseModel
        return $this->prefixImgUrl($value);
    }

    // 查询范围 查询状态为1的
    public function scopeUserStatus($query)
    {
        // 继承 BaseModel
        return $this->scopeStatus($query, 'user_status');
    }

    // 用户角色 一对多
    public function roles()
    {
        return $this->belongsToMany('Role', 'user_role_rel','role_id', 'user_id');
    }


    /**
     *  获取用户的角色和权限
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOneAuths($id)
    {
        $auths = self::scope('user_status')->with(['roles', 'roles.auths'])
            ->find($id);
        $auths = $auths['roles']->where('role_status', '=', 1)->visible(['auths'=>['auth_route']])->toArray();
        return $auths;
    }



}