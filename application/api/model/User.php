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
    protected $hidden = ['update_time', 'delete_time', 'ext'];
    // 显示字段
//    protected $visible = ['id', 'user_name', 'user_photo'];

    // 读取器 get-UserPhoto(数据库字段 驼峰命名法)-Attr(固定写法)
    // 设置完整图片路径
    protected function getUserPhotoAttr($value)
    {
        // 继承 BaseModel
        return $this->prefixImgUrl($value);
    }

    protected function getLastLoginTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
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
        return $this->belongsToMany('Role', '\\app\\api\\model\\UserRole','role_id', 'user_id');
    }

    // 用户详细信息 一对一
    public function userDetail()
    {
        return $this->hasOne('UserDetail', 'user_id', 'id');
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
        $auths = $auths['roles']->where('role_status', '=', 1)->visible(['auths'=>['auth_route', 'auth_status']])->toArray();
        return $auths;
    }


    // 检测用户名是否存在
    public static function userNameIsExist($field, $user_name)
    {
        $user = self::where($field, '=', $user_name)
            ->find();
        return $user;
    }


    /**
     * 展示所有用户+用户详情 分页+条件+排序
     * @param $wheres 前端传过来的条件 $wheres [page:1, pageSize: 15, where:[], order:[]]
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function userCondition($wheres)
    {
//        $conditions = self::whereList($wheres);
//        $pageDate = self::where($conditions['where'])
//            ->order($conditions['order'])
//            ->order('create_time','desc')
//            ->paginate($conditions['pageSize'], false, ['page'=>$conditions['page']]);
//        return $pageDate;
        $whereFields = [
            ['user_mobile', 'like', ''],
            ['user_name',   'like', ''],
            ['user_email',  'like', ''],
            ['user_status', '=', ''],
        ];
        $orderFields = [
            'login_count'     => '',
            'last_login_time' => '',
        ];
        $conditions = self::splicingCondition($wheres, $whereFields, $orderFields);
        $pageDate = self::where($conditions['where'])
            ->order($conditions['order'])
            ->order('create_time','desc')
            ->with('userDetail')
            ->paginate($conditions['pageSize'], false, ['page'=>$conditions['page']]);
        return $pageDate;
//        return self::paging($wheres, $whereFields, $orderFields);
    }


}