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

    // 查询范围  status状态范围的查询
    protected function scopeStatus($query, $field)
    {
        $query->where($field, '=', 1);
    }


    /**
     * 处理页码+条件+排序
     * @param $condition 条件 [page:1,pageSize:15, where:[], order:[]]
     * @param array $whereFields where字段
     * @param array $orderFields order字段
     * @return array
     */
    private static function splicingCondition($condition, $whereFields = [], $orderFields = [])
    {
        $page = self::splicingPage($condition);

        $where = [];
        if( (!empty($condition['where'])) && (!empty($whereFields)) ){
            $w = self::jsonToArray($condition['where']);
            $where = self::splicingWhere($w, $whereFields);
        }
        $page['where'] = $where;

        $order = [];
        if( (!empty($condition['order'])) && (!empty($orderFields)) ){
            $o = self::jsonToArray($condition['order']);
            $order = self::splicingOrder($o, $orderFields);
        }
        $page['order'] = $order;

        return $page;
    }

    // paginate
    protected static function paging($condition, $whereFields = [], $orderFields = [])
    {
        $conditions = self::splicingCondition($condition, $whereFields, $orderFields);
        $pageDate = self::where($conditions['where'])
            ->order($conditions['order'])
            ->order('create_time','desc')
            ->paginate($conditions['pageSize'], false, ['page'=>$conditions['page']]);
        return $pageDate;
    }


    // json数组处理
    private static function jsonToArray($data)
    {
        if(is_array($data)){
            $arr = $data;
        } else {
            $arr = json_decode($data);
        }
        return $arr;
    }

    // 处理页码,每页数量
    private static function splicingPage($wheres = [])
    {
        if(empty($wheres['page'])){
            $page = 1;
        } else {
            $page = $wheres['page'];
        }
        if(empty($wheres['pageSize'])){
            $pageSize = 15;
        } else {
            $pageSize = $wheres['pageSize'];
        }
        return [
            'page'=>$page,
            'pageSize'=>$pageSize
        ];
    }

    // 拼接where条件
    private static function splicingWhere($wheres = [], $whereFields = [])
    {
        $where = [];
        foreach ($whereFields as $k=>$v){
            if(!empty($wheres[$k])){
                if($v[1] == 'like'){
                    $v[2] = '%'.$wheres[$k].'%';
                }
                $where[] = $v;
            }
        }
        return $where;
    }

    // 拼接order排序
    private static function splicingOrder($orders = [], $orderFields = [])
    {
        $order = [];
        foreach ($orderFields as $k=>$v){
            if(!empty($orders[$k])){
                $order[$k] = $orders[$k];
            }
        }
        return $order;
    }

}