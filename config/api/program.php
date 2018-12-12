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
    // 用户默认头像
    'default_photo'=>'s.jpg',

    // 图片地址前缀
    'img_prefix'=> 'http://user.com/static/images/',
    'static_image'=> __DIR__.'/../../public/static/images/',

    // excel
    'excel_prefix'=>'http://user.com/static/excel/',
    'static_excel'=> __DIR__.'/../../public/static/excel/',

    // 缓存时间
    'token_expire_in' => 7200,
    // 微信支付回调地址
    'pay_back_url'=> 'http://user.com/api/pay/notify'
];

// 获取 config('program.static_excel')