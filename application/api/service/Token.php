<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 16:33
 */

namespace app\api\service;


use think\Exception;
use think\facade\Cache;
use think\facade\Request;
use app\lib\exception\TokenException;
use app\lib\exception\ForbiddenException;
use app\lib\enum\ScopeEnum;

class Token
{
    /**
     * 生成32位随机Token
     * @return string
     */
    public static function generateToken()
    {
        // 32个字符组成一组随机字符串
        $randChars = getRandChars(32);
        // 用三组字符串进行MD5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars . $timestamp . $salt);
    }


    /**
     *  获取当前用户的id
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUid()
    {
        return self::getCurrentTokenVar('uid');
    }


    /**
     *  获取当前用户的routes
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentRoutes()
    {
        return self::getCurrentTokenVar('routes');
    }


    /**
     *  根据key获取token权限里的value
     * @param $key
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar($key)
    {
        $token = Request::header('token');
        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();
        } else {
            if(!is_array($vars)){
                $vars = json_decode($vars, true);
                if(array_key_exists($key, $vars)){
                    return $vars[$key];
                } else {
                    throw new Exception('尝试获取token变量并不存在');
                }
            }

        }
    }


    /**
     *  对用户和管理员都有的权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            } else {
                // 权限不够
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }


    /**
     *  仅对用户的权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            } else {
                // 权限不够
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }


    /**
     *  仅对管理员的权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needSuperScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::Super){
                return true;
            } else {
                // 权限不够
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }


    /**
     *  检查uid
     * @param $uid
     * @return bool
     * @throws Exception
     * @throws TokenException
     */
    public static function isValidOperate($uid)
    {
        if(!$uid){
            throw new Exception('检查uid时,请传入uid');
        }
        $currentUid = self::getCurrentUid();
        if($currentUid == $uid){
            return true;
        }
        return false;
    }


    /**
     * 检测Token是否存在
     * @param string $token
     * @return bool
     */
    public static function verifyToken($token = '')
    {
        $exist = Cache::get('token');
        if($exist){
            return true;
        } else {
            return false;
        }
    }

}