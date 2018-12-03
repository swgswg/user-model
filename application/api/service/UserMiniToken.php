<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 14:11
 */

namespace app\api\service;


use app\api\model\UserMini;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WxChatException;
use think\Exception;

class UserMiniToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('weixin.app_id');
        $this->wxAppSecret = config('weixin.app_secret');
        $this->wxLoginUrl = sprintf(config('weixin.login_url'),$this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get()
    {
        // $result = curl_get($this->wxLoginUrl);
        // {openid:"oWXIA5fhg5-BLAYL5B8QEXAznhNE",session_key:"Nqa2VZ8Cn4hXvlKmEzAwFA=="}
        // $wxResult = json_decode($result, true);
        $wxResult = [
            'openid'=>'oWXIA5fhg5-BLAYL5B8QEXAznhNE',
            'session_key'=>'Nqa2VZ8Cn4hXvlKmEzAwFA=='
        ];
        if(empty($wxResult)){
            throw new Exception('获取session_key及openID时异常, 微信内部错误');
        } else {
            $loginFail = array_key_exists('errcoode',$wxResult);
            if($loginFail){
                // 调用错误
                $this->processLoginError($wxResult);

            } else {
                return $this->grantToken($wxResult);
            }
        }
    }


    /**
     * 授予Token
     * @param $wxResult
     * @return string
     * @throws TokenException
     */
    private function grantToken($wxResult)
    {
        // 拿到openid
        // 查看数据库是否存在openid
        // 存在不处理, 不存在新增一条记录
        // 生成令牌, 准备缓存数据, 写入缓存
        // 把令牌返回到客户端
        // key:令牌
        // value: wxResult, uid, scope
        $openid = $wxResult['openid'];
        $userMini = UserMini::getByOpenId($openid);
        if($userMini){
            $uid = $userMini->id;
        } else {
            $uid = $this->newUserMini($openid);
        }
        $cacheValue = $this->prepareCacheValue($wxResult, $uid);
        $token = $this->saveToCache($cacheValue);
        return $token;
    }


    /**
     *  生成一个新微信用户
     * @param $openid
     * @return mixed
     */
    private function newUserMini($openid)
    {
        $userMini = UserMini::create([
            'openid' => $openid
        ]);
        return $userMini->id;
    }


    /**
     *  准备缓存值
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCacheValue($wxResult, $uid)
    {
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;
        // scope=16代表用户权限
        $cacheValue['scope'] = ScopeEnum::User;
        return $cacheValue;
    }


    /**
     *  保存到缓存值
     * @param $cacheValue
     * @return string
     * @throws TokenException
     */
    private function saveToCache($cacheValue)
    {
        $key = self::generateToken();
        $value = json_encode($cacheValue);
        $expire_in = config('program.token_expire_in');
        $request = cache($key, $value, $expire_in);
        if(!$request){
            throw new TokenException([
                'message'=>'服务器缓存异常',
                'errorCode'=>10005
            ]);
        }
        return $key;
    }

    /**
     *  微信错误
     * @param $wxResult
     * @throws WxChatException
     */
    private function processLoginError($wxResult)
    {
        throw new WxChatException([
            'message' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}