<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/8
 * Time: 15:22
 */

namespace app\api\model;

use think\model\Pivot;
use think\model\concern\SoftDelete;

class AdminRole extends Pivot
{
    protected $table = 'admin_role_rel';
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