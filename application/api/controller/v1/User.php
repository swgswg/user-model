<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:46
 */

namespace app\api\controller\v1;


use think\Request;

use app\api\service\SignIn as SignInService;
use app\lib\exception\UserException;
use app\api\validate\LoginUserNameRequire;
use app\api\validate\SignUpDataValidate;
use app\api\controller\v1\common\Output;

class User extends BaseController
{

    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @param Request $request
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function signIn(Request $request)
    {
        // 用户名 密码验证
        // {"message":"没有用户名你还想登录!!!,没有密码你还想登录!!!","state":0,"error_code":10000,"request_url":"http:\/\/user.com\/login"}
        (new LoginUserNameRequire())->goCheck();

        // 登录
        $data = SignInService::signIn('user');
        if(!$data){
            // {"message":"用户不存在","state":0,"error_code":20000,"request_url":"http:\/\/user.com\/login"}
            throw new UserException();
        }

        // {"message":"登录成功","state":1,"data":[],"error_code":"request:ok"}
        return Output::out($data,'登录');
    }


    /**
     *  注册
     * @param Request $request
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function signUp(Request $request)
    {
        // 注册信息验证
        // {"message":"手机号码格式不正确,验证码必须是6位数,用户名长度在4-25之间,密码长度在6-25之间","state":0,"error_code":10000,"request_url":"http:\/\/user.com\/signUp"}
        (new SignUpDataValidate())->goCheck();
        $params = $request->param();
        /** 验证手机验证码 */
        unset($params['code']);
        try{
            $data = SignInService::signUp('user',$params);
        }catch (\Exception $e){
            throw $e;
        }

        // 给注册用户角色 默认用户普通会员
        // $this->ascribedRole($data['id']);

        // {"message":"注册成功","state":1,"data":{"user_name":"176asdfa","user_photo":"http:\/\/user.com\/static\/images\/aaa.png","id":"6"},"error_code":"request:ok"}
        return Output::out($data,'注册');
    }




}