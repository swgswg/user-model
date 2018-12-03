<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 14:43
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use think\facade\Request;
use app\api\model\User as UserModel;
use app\api\model\UserDetail as UserDetailModel;
use app\api\service\Token as TokenService;
use app\api\controller\v1\common\Output;
use app\api\validate\addUserDetailValidate;
use app\lib\exception\IllegalOperation;
use app\lib\exception\UserException;

class UserDetail extends BaseController
{
    /**
     *  获取单个用户详细信息
     * @param  user_id 用户id
     * @return mixed
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\Exception\DbException
     */
    public function oneUserDetail()
    {
        $user_id = TokenService::getCurrentUid();
        $userDetail = UserDetailModel::where('user_id', '=', $user_id)
            ->find();
        if(!$userDetail){
            throw new UserException([
                'message' => '获取用户详情失败',
                'errorCode' => 20004
            ]);
        }
        return Output::out('获取用户详情', $userDetail);
    }


    /**
     *  修改单个用户详细信息(用户自己操作)
     * @param user_id 用户id
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function updateUserDetail()
    {
        $user_id = TokenService::getCurrentUid();
        $validate = new addUserDetailValidate();
        $validate->goCheck();
        // 根据规则取字段是很有必要的，防止恶意更新非客户端字段
        $detail = $validate->getDataByRule(Request::post());
        $this->createOrUpdate($user_id, $detail);
        Output::out('修改用户详情');
    }


    /**
     *  修改单个用户详细信息(管理员操作)
     * @param id 用户id(必传且为id, 一定不是user_id, uid)
     * @throws IllegalOperation
     * @throws UserException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\Exception\DbException
     */
    public function editUserDetail()
    {
        (new IDMustBePositiveInt())->goCheck();

        $uid = TokenService::getCurrentUid();
        $user_id = Request::post('id');
        if($uid == $user_id){
            // 管理员修改用户详情的用户id不能跟自己id相同, 否则就是用户自己操作, 属于非法操作
            throw new IllegalOperation();
        }
        $validate = new addUserDetailValidate();
        $validate->goCheck();
        // 根据规则取字段是很有必要的，防止恶意更新非客户端字段
        $detail = $validate->getDataByRule(Request::post());
        $this->createOrUpdate($user_id, $detail);
        Output::out('修改用户详情');
    }



    /**
     *  更新或者修改用户详情
     * @param $user_id 用户id
     * @param $detail  详情字段
     * @throws UserException
     * @throws \think\Exception\DbException
     */
    private function createOrUpdate($user_id, $detail)
    {
        $user = UserModel::get($user_id);
        if(!$user){
            // 用户不存在
            throw new UserException();
        }
        $userDetail = $user->userDetail;
        if(!$userDetail){
            // 用户详情不存在
            // 关联属性不存在，则新建
            $user->userDetail()->save($detail);
        } else {
            // 存在则更新
            // 新增的save方法和更新的save方法并不一样
            // 新增的save来自于关联关系
            // 更新的save来自于模型
            $user->userDetail->save($detail);
        }
    }

}