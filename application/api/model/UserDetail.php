<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 14:14
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class UserDetail extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'user_detail';
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
    protected $hidden = ['id', 'create_time', 'update_time', 'delete_time', 'ext'];


    // 读取器 get-UserPhoto(数据库字段 驼峰命名法)-Attr(固定写法)
    // 设置完整性别
//    protected function getSexAttr($value)
//    {
//        if($value == 1) {
//            return '男';
//        } else if($value == 2) {
//            return '女';
//        } else {
//            return '未知';
//        }
//    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }
}