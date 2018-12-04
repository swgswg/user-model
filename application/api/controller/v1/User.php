<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:46
 */

namespace app\api\controller\v1;


use think\facade\Request;
use app\api\model\User as UserModel;
use app\api\service\SignIn as SignInService;
use app\api\service\SignUp as SignUpService;
use app\lib\exception\UserException;
use app\api\validate\SignInValidate;
use app\api\validate\SignUpValidate;
use app\api\validate\UserEmailValidate;
use app\api\validate\UserMobileValidate;
use app\api\validate\UserNameValidate;
use app\api\validate\SplicingConditionValidate;
use app\api\controller\v1\common\Output;

class User extends BaseController
{

    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @param user_name
     * @param user_pass
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function signIn()
    {
        // 用户名 密码验证
        // {"message":"没有用户名你还想登录!!!,没有密码你还想登录!!!","state":0,"error_code":10000,"request_url":"http:\/\/user.com\/login"}
        (new SignInValidate())->goCheck();

        // 登录
        $data = SignInService::signIn();

        return Output::out('登录', $data);
    }


    /**
     *  注册
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function signUp()
    {
        // 注册信息验证
        // {"message":"手机号码格式不正确,验证码必须是6位数,用户名长度在4-20之间,密码长度在6-20之间","state":0,"error_code":10000,"request_url":"http:\/\/user.com\/signUp"}
        $validate = new SignUpValidate();
        $validate->goCheck();

        // 获取验证的字段, 过滤非法字段
        $params = $validate->getDataByRule(Request::post());
        // 去除重复密码字段
        unset($params['repassword']);
        /** 验证手机验证码 */
        unset($params['code']);

        // 注册
        SignUpService::signUp($params);

        // {"message":"注册成功","state":1,"data":{"user_name":"176asdfa","user_photo":"http:\/\/user.com\/static\/images\/aaa.png","id":"6"},"error_code":"request:ok"}
        return Output::out('注册');
    }


    /**
     *  检测用户名是否存在
     * @param user_name
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userNameIsExist()
    {
        (new UserNameValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post());
    }


    /**
     *  检测用户手机号是否存在
     * @param user_mobile
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userMobileIsExist()
    {
        (new UserMobileValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post());
    }


    /**
     *  检测用户是否存在
     * @param user_email
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userEmailIsExist()
    {
        (new UserEmailValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post());
    }


    /**
     *  获取所有用户  分页+条件+排序
     * @param page (不传默认1)
     * @param pageSize (不传默认15)
     * @param where 条件数组
     * @param order 排序数组
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        (new SplicingConditionValidate())->goCheck();
        $users = UserModel::userCondition(Request::post());
        // $currentPage = $users->currentPage(); 当前页
        if($users->isEmpty()){
            return Output::out('获取所有用户', []);
        }
        $data = $users->hidden(['user_pass', 'token', 'ext', 'update_time', 'delete_time'])
            ->toArray();
        return Output::out('获取所有用户', $data);
    }


    // 修改用户状态

    // 添加用户角色

    // 删除用户角色






}