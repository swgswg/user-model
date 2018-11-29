<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 17:07
 */

namespace app\api\model;


use think\Model;
use think\model\concern\SoftDelete;

class Test extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'think_user';
    protected $pk = 'id';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 关闭自动写入update_time字段
//    protected $updateTime = false;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public static function getTestById($id)
    {
//        try{
//            1/0;
//        } catch (Exception $ex){
//            throw $ex;
//        }
//        return 'this is test';
        return $id;

    }
}