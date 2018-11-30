<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 14:09
 */

namespace app\api\model;


class UserMini extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'user_mini';
    protected $pk = 'id';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public $visible = ['id', 'nikename', 'profile'];

    public static function getByOpenId($openid)
    {
       $userMini = self::where('openid', '=', $openid)
           ->find();
       return $userMini;
    }
}