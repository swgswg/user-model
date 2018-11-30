<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/29
 * Time: 9:15
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    // 读取器 get-UserPhoto(数据库字段user_photo 驼峰命名法)-Attr(固定写法)
    // 设置完整图片路径 ($value, $data)
    protected function prefixImgUrl($value)
    {
        return config('program.img_prefix').$value;
    }

    // 获取用户权限


    // 查询范围  status状态范围的查询
    protected function scopeStatus($query, $field)
    {
        $query->where($field, '=', 1);
    }
}