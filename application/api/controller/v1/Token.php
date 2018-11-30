<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 13:51
 */

namespace app\api\controller\v1;


use app\api\controller\v1\common\Output;
use app\api\service\UserMiniToken;
use app\api\validate\TokenGet;
use think\Request;

class Token extends BaseController
{
    public function getToken(Request $request)
    {
        (new TokenGet())->goCheck();

        $ut = new UserMiniToken($request->param('code'));
        $token = $ut -> get();
        return Output::out($token, '获取Token');
    }
}