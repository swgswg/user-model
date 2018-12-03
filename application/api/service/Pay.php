<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/1
 * Time: 20:20
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Log;
use think\Loader;
use think\facade\Env;

Loader::import('WxPay.WxPay',Env::get('root_path') . 'extend/', '.Api.php');

class Pay
{
    private $orderId;
    private $orderNo;

    public function __construct($orderId)
    {
        if(!$orderId){
            throw new Exception('订单号不能为空');
        }
        $this->orderId = $orderId;
    }


    public function pay()
    {
        // 检测订单号是否存在
        // 订单号存在, 但是订单号和当前用户不匹配
        // 订单有可能已经被支付
        // $this->checkOrderValid();

        /** 根据订单号查询对应商品的库存并获取 总金额 */
        $totalPrice = 1;
        $this->makeWxPreOrder($totalPrice);
    }


//    private function checkOrderValid()
//    {
//        $order = OrderModel::where('id', '=', $this->orderId)
//            ->find();
//        if($order){
//            throw new OrderException();
//        }
//
//        if(!Token::isValidOperate($order->user_id)){
//            throw new TokenException([
//                'message'=>'订单与用户不匹配',
//                'errorCode' => 10003
//            ]);
//        }
//
//        // 订单没有支付
//        if($order->status != OrderStatusEnum::UNPAIN){
//           throw new OrderException([
//               'code'      =>400,
//               'message'   =>'订单已支付',
//               'errorCode' => 80003
//           ]);
//        }
//
//        $this->orderNo = $order->order_no;
//        return true;
//    }



    private function makeWxPreOrder($totalPrice)
    {
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData = new \WxPay\WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSPAI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('program.pay_back_url'));  // 回掉地址 后期一定要加上

        return $this->getPaySignature($wxOrderData);
    }


    /**
     *  向微信请求订单号并生成签名
     * @param $wxOrderData
     * @return bool
     * @throws \WxPay\WxPayException
     */
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPay\WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS'){
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }
        $signature = $this->sign($wxOrder);
        return $signature;
    }


    /**
     *  生成微信签名
     * @param $wxOrder
     * @return mixed
     */
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

}