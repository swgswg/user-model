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

    public $hidden = ['update_time', 'delete_time', 'ext'];
//    public $visible = ['id', 'auth_name', 'auth_route', 'auth_route_version', 'auth_status'];

    // 查询范围 auth_status为1的
    public function scopeAuthStatus($query)
    {
        // 继承 BaseModel
        return $this->scopeStatus($query, 'auth_status');
    }


    /**
     * 根据条件获取所有的权限 分页+条件+排序
     * @param $wheres [page:1, pageSize: 15, where:[], order:[]]
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function authCondition($wheres)
    {
        $conditions = self::whereList($wheres);
        $pageData = self::where($conditions['where'])
            ->order($conditions['order'])
            ->order('create_time', 'desc')
            ->paginate($conditions['pageSize'], false, ['page'=>$conditions['page']]);
        return $pageData;
    }

    // 拼接条件 继承基类
    private static function whereList($wheres)
    {
        $whereFields = [
            'auth_route'   =>['auth_route', 'like', ''],
            'auth_version' =>['auth_route_version', 'like', ''],
            'auth_name'    =>['auth_name', 'like', ''],
            'auth_status'  =>['auth_status', '=', ''],
        ];

        $orderFields = [
            'auth_version' => '',
            'auth_order'   => '',
        ];
        $conditions = self::splicingCondition($wheres,$whereFields, $orderFields);
        return $conditions;
    }


}