<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/29
 * Time: 19:26
 */

namespace app\api\controller\v1;



use app\api\validate\IDMustBePositiveInt;
use app\api\validate\user\AddUserRolesValidate;
use think\facade\Request;
use app\api\model\Admin as AdminModel;
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

class Admin extends BaseController
{
    /**
     * 管理员登录(可选管理员名/电话/邮箱)
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
        (new SignInValidate())->goCheck();

        // 登录
        $data = SignInService::signIn(2);

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
        // {"message":"手机号码格式不正确,验证码必须是6位数,管理员名长度在4-20之间,密码长度在6-20之间","state":0,"error_code":10000,"request_url":"http:\/\/user.com\/signUp"}
        $validate = new SignUpValidate();
        $validate->goCheck();

        // 获取验证的字段, 过滤非法字段
        $params = $validate->getDataByRule(Request::post());
        // 去除重复密码字段
        unset($params['repassword']);
        /** 验证手机验证码 */
        unset($params['code']);

        // 注册
        SignUpService::signUp($params, 2);

        // {"message":"注册成功","state":1,"data":{"user_name":"176asdfa","user_photo":"http:\/\/user.com\/static\/images\/aaa.png","id":"6"},"error_code":"request:ok"}
        return Output::out('注册');
    }


    /**
     *  检测管理员名是否存在
     * @param nickname
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userNameIsExist()
    {
        (new UserNameValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post(),2);
    }


    /**
     *  检测管理员手机号是否存在
     * @param mobile
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userMobileIsExist()
    {
        (new UserMobileValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post(), 2);
    }


    /**
     *  检测管理员是否存在
     * @param email
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     */
    public function userEmailIsExist()
    {
        (new UserEmailValidate())->goCheck();
        SignUpService::userNameIsExist(Request::post(), 2);
    }


    /**
     *  获取所有管理员  分页+条件+排序
     * @param page (不传默认1)
     * @param pageSize (不传默认15)
     * @param where 条件数组
     * @param order 排序数组
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function index()
    {
        (new SplicingConditionValidate())->goCheck();
        $users = AdminModel::userCondition(Request::post());
        // $currentPage = $users->currentPage(); 当前页
        if($users->isEmpty()){
            return Output::out('获取所有管理员', []);
        }
        $data = $users->hidden(['password', 'token', 'ext', 'update_time', 'delete_time'])
            ->toArray();
        return Output::out('获取所有管理员', $data);
    }


    /**
     * 修改管理员状态
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
                'message'=>'修改管理员状态失败',
                'errorCode'=>50001
            ]);
        }
        return Output::out('状态修改');
    }


    /**
     * 获取管理员所有角色
     * @param id 管理员id
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
            return Output::out('获取管理员角色', []);
        }
        $all = $roles->visible([
            'id',
            'role_name',
            'role_desc',
            'role_order',
//            'role_status',
            'role_group',
            'pivot.id',
            'pivot.rel_status',
            'pivot.create_time'
        ])->toArray();
        return Output::out('获取管理员角色', $all);
    }


    /**
     * 添加管理员角色 可一次添加多个
     * @param id 管理员id
     * @param user_role 角色数组[1,2,3]
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
            return Output::out('添加成功');
        } else {
            throw new UserException([
                'code'=>400,
                'message'=> '添加管理员角色失败',
                'errorCode' => 50002
            ]);
        }
    }


    /**
     * 删除管理员角色 可批量操作
     * @param id 管理员id
     * @param user_role 删除管理员角色 数组
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
                'message'=> '删除管理员角色失败',
                'errorCode' => 50003
            ]);
        }
    }


    // 获取管理员
    private function getUser($id)
    {
        $user = AdminModel::get($id);
        if(!$user){
            throw new UserException();
        }
        return $user;
    }
}