<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 17:30
 */

namespace app\api\validate\user;

use app\api\validate\BaseValidate;

class SignInValidate extends BaseValidate
{
    protected $rule = [
        'nickname' => 'require|isNotEmpty|userNameValid',
        'password' => 'require|isNotEmpty|length:6,20'
    ];

    protected $message=[
        'nickname.require' => '没有用户名你还想登录!!!',
        'password.require' => '没有密码你还想登录!!!'
    ];

    /**
     *  检测用户名
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected function userNameValid($value, $rule = '', $data = '', $field = '')
    {
        $telReg = '/^1[3456789]\d{9}$/';
        $emailReg = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        $string = '/^\w{4,20}$/';

        if(preg_match($telReg,$value)){
            // 手机号登录
            return true;
        } else if(preg_match($emailReg,$value)) {
            // 邮箱登录
            return true;
        } else if(preg_match($string,$value)) {
            // 用户名登录
            return true;
        } else {
            return $field.'不符合要求';
        }

    }
}