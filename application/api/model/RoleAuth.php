<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/9
 * Time: 13:52
 */

namespace app\api\model;


use think\model\Pivot;
use think\model\concern\SoftDelete;

class RoleAuth extends Pivot
{
    protected $table = 'role_auth_rel';
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['update_time', 'delete_time'];
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}