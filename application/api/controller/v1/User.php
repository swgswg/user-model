<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 13:46
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\validate\user\AddUserRolesValidate;
use app\lib\exception\ParameterException;
use think\facade\Request;
use app\api\model\User as UserModel;
use app\api\service\SignIn as SignInService;
use app\api\service\SignUp as SignUpService;
use app\lib\exception\UserException;
use app\api\validate\user\SignInValidate;
use app\api\validate\user\SignUpValidate;
use app\api\validate\user\UserEmailValidate;
use app\api\validate\user\UserMobileValidate;
use app\api\validate\user\UserNameValidate;
use app\api\validate\SplicingConditionValidate;
use app\api\validate\EditStatusValidate;
use app\api\controller\v1\common\Output;

class User extends BaseController
{

    /**
     *  用户登录(可选用户名/电话/邮箱)
     * @param nickname
     * @param password
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
        $data = SignInService::signIn(1);

        return Output::out('登录', $data);
    }


    /**
     *  注册
     * @param nickname 昵称
     * @param mobile   手机号
     * @param code     手机验证码
     * @param email    邮箱(可选)
     * @param photo    头像(可选)
     * @param password 密码
     * @param repassword 重复密码
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
        SignUpService::signUp($params, 1);

        // {"message":"注册成功","state":1,"data":{"user_name":"176asdfa","user_photo":"http:\/\/user.com\/static\/images\/aaa.png","id":"6"},"error_code":"request:ok"}
        return Output::out('注册');
    }


    /**
     *  检测用户名是否存在
     * @param nickname
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userNameIsExist()
    {
        (new UserNameValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post(),1);
    }


    /**
     *  检测用户手机号是否存在
     * @param mobile
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userMobileIsExist()
    {
        (new UserMobileValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post(), 1);
    }


    /**
     *  检测用户是否存在
     * @param email
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userEmailIsExist()
    {
        (new UserEmailValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post(), 1);
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
        $data = $users->hidden(['password', 'token', 'ext', 'update_time', 'delete_time'])
            ->toArray();
        return Output::out('获取所有用户', $data);
    }


    /**
     * 修改用户状态
     * @param id
     * @param status
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function editStatus()
    {
        (new EditStatusValidate())->goCheck();
        $user = $this->getUser(Request::post('id'));
        $user -> status = Request::post('status');
        $res = $user->save();
        if(!$res){
            throw new UserException([
                'message'=>'修改用户状态失败',
                'errorCode'=>20008
            ]);
        }
        return Output::out('状态修改');
    }


    /**
     * 获取用户所有角色
     * @param id 用户id
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function allRoles()
    {
        (new IDMustBePositiveInt())->goCheck();
        $user = $this->getUser(Request::post('id'));
        $roles = $user->roles;
        if($roles->isEmpty()){
            return Output::out('获取用户角色', [
                'roleIds'=>[],
                'all'=>[]
            ]);
        }
        $all = $roles->visible([
            'id',
            'role_name',
            'role_desc',
            'role_order',
            'role_group',
//            'role_status',
//            'pivot.id',
//            'pivot.rel_status',
//            'pivot.create_time'
        ])->toArray();
        $roleIds = [];
        foreach ($all as $k=>$v){
            array_push($roleIds,$v['id']);
        }
        $roless['roleIds'] = $roleIds;
        $roless['all'] = $all;

        return Output::out('获取用户角色', $roless);
    }


    /**
     * 添加用户角色 可一次添加多个
     * @param id 用户id
     * @param user_role 角色json数组[1,2,3]
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function addUserRoles()
    {
        (new AddUserRolesValidate())->goCheck();
        $user = $this->getUser(Request::post('id'));
        $user_role = Request::post('user_role');
        if(!is_array($user_role)){
            $user_role = json_decode($user_role);
        }
        $res = $user->roles()->saveAll($user_role);
        if($res){
            return Output::out('添加');
        } else {
            throw new UserException([
                'code'=>400,
                'message'=> '添加用户角色失败',
                'errorCode' => 20007
            ]);
        }
    }


    /**
     * 删除用户角色 可批量操作
     * @param id 用户id
     * @param user_role 删除用户角色 数组
     * @return \think\response\Json
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function deleteUserRole()
    {
        (new AddUserRolesValidate())->goCheck();
        $user = $this->getUser(Request::post('id'));
        $user_role = Request::post('user_role');
        if(!is_array($user_role)){
            $user_role = json_decode($user_role);
        }
        $res = $user->roles()->detach($user_role);
        if($res){
            return Output::out('删除成功');
        } else {
            throw new UserException([
                'code'=>400,
                'message'=> '删除用户角色失败',
                'errorCode' => 20006
            ]);
        }
    }


    // 获取用户
    private function getUser($id)
    {
        $user = UserModel::get($id);
        if(!$user){
            throw new UserException();
        }
        return $user;
    }


}