<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 14:13
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{

    public function goCheck()
    {
        // 获取http传入的参数
        // 对参数做校验
        $params = Request::param();
        var_dump($params);
        $res = $this->batch()->check($params);
        if(!$res){
            $err = $this->getError();
            if(is_array($err)){
                $err = implode(',',$err);
            }
            $e = new ParameterException([
                'message'=>$err
            ]);
//            $e->message = $this->getError();
            throw $e;
        } else {
            return true;
        }
    }


    /**
     *  必须是正整数
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected function isPositiveInteger($value, $rule='', $data='', $field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return $field . '必须是正整数';
    }


    /**
     *  不能是空
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return $field . '不允许为空';
        } else {
            return true;
        }
    }


    /**
     * 手机号的验证规则
     * 没有使用TP的正则验证，集中在一处方便以后修改
     * 不推荐使用正则，因为复用性太差
     * @param $value
     * @return bool
     */
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}