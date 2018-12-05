<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 13:51
 */

namespace app\api\controller\v1;


use app\api\service\UserToken as UserTokenService;
use app\lib\exception\ParameterException;
use think\Request;
use app\api\service\UserMiniToken;
use app\api\validate\TokenGet;
use app\api\validate\user\SignInValidate;
use app\api\service\SignIn as SignInService;
use app\api\controller\v1\common\Output;
use app\api\service\Token as TokenService;

class Token extends BaseController
{
    /**
     *  微信小程序获取token
     * @param Request $request
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function miniGetToken(Request $request)
    {
        (new TokenGet())->goCheck();

        $ut = new UserMiniToken($request->param('code'));
        $token = $ut -> get();
        return Output::out($token, '获取Token');
    }


    /**
     * 登录获取token
     */
    public function signInGetToken()
    {
        (new SignInValidate())->goCheck();
        $user = SignInService::signIn();
        $ut = new UserTokenService($user['id']);
        $token = $ut -> get();
        return Output::out('获取Token', $token);
    }


    /**
     * 检测token
     * @param token
     * @return array
     * @throws ParameterException
     */
    public function verifyToken()
    {
        $token = Request::post('token');
        if(!$token){
            throw new ParameterException([
                'code'=> 403,
                'message'=>'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        $data =  ['isValid' => $valid];
        return Output::out('检测token', $data);
    }


}