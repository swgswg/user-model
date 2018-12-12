<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:53
 */

namespace app\api\model;


use app\lib\exception\AuthException;
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
        $this->scopeStatus($query, 'auth_status');
    }

    public static function init()
    {
        self::event('before_insert', function ($auth) {
            $auth = self::routeExist($auth->auth_route);
            if($auth){
                throw new AuthException([
                    'message'=>'权限路由已经存在,不要重复添加',
                    'errorCode'=> 40002
                ]);
            } else {
                return true;
            }
        });
    }

    /**
     * 根据条件获取所有的权限 分页+条件+排序
     * @param $wheres [page:1, pageSize: 15, where:[], order:[]]
     * @return \think\Paginator
     */
    public static function authCondition($wheres)
    {
//        $conditions = self::whereList($wheres);
//        $pageData = self::where($conditions['where'])
//            ->order($conditions['order'])
//            ->order('create_time', 'desc')
//            ->paginate($conditions['pageSize'], false, ['page'=>$conditions['page']]);
//        return $pageData;
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
        return self::paging($wheres, $whereFields, $orderFields);
    }


    // 判断路由是否存在
    public static function routeExist($auth_route)
    {
        $auth = self::where('auth_route', '=', $auth_route)->find();
        return $auth;
    }


    // 根据条件获取权限
    public static function authWhere($where = [])
    {
        $auths = self::where($where)
            ->order('create_time', 'desc')
            ->select();
        return $auths;
    }
}