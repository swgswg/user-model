<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 11:55
 */

namespace app\api\controller\v1;


use think\Controller;
use think\Request;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use app\api\model\Test as TestModel;
use app\api\controller\common\Output;

class Test extends Controller
{
    public function getData(Request $request)
    {
        (new IDMustBePositiveInt())->goCheck();
        $test = TestModel::getTestById($request->param('id'));
        if(!$test){
            throw new MissException();
        }
        return Output::out($test,'获取');
    }


    public function test(Request $request)
    {
        (new IDMustBePositiveInt())->goCheck();
        $test = TestModel::getTestById($request->param('id'));
        if(!$test){
            throw new MissException();
        }
        return Output::out($test,'获取');
    }
}