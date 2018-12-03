<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 23:18
 */

// +----------------------------------------------------------------------
// | 项目自定义配置
// +----------------------------------------------------------------------

return [
    // 图片地址前缀
    'img_prefix'=> 'http://user.com/static/images/',
    // 缓存时间
    'token_expire_in' => 7200,
    // 微信支付回调地址
    'pay_back_url'=> 'http://user.com/api/pay/notify'
];

// 获取 config('program.pay_back_url')