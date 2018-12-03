<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/1
 * Time: 20:19
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPerOrder']
    ];

    public function getPerOrder($id = '')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }


    /**
     *  断点调试微信回调
     */
    public function redirectNotify()
    {
        $notify = new WxNotify();
        $notify->handle();
    }

    public function notifyConcurrency()
    {
        $notify = new WxNotify();
        $notify->handle();
    }

    /**
     * 微信回调
     */
    public function receiveNotify()
    {
        // 通知频率 15/15/30/180/1800/1800/1800/1800/3600s

        // 检查库存量
        // 更新订单的status
        // 减库存
        // 成功处理, 返回微信成功处理信息, 维信终止回调

//        Log::error($xmlData);
        $notify = new WxNotify();
        $notify->handle();

        // 调试微信返回值
//        $xmlData = file_get_contents('php://input');
//        $result = curl_post_raw('http:/zerg.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133',
//            $xmlData);
//        return $result;
//        Log::error($xmlData);
    }
}