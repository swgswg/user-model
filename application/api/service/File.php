<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/11
 * Time: 13:39
 */

namespace app\api\service;

use app\lib\exception\FileUploadException;

class File
{

    /**
     * 获取文件大小
     * @param $fileInfo
     * @return mixed
     */
    protected static function getSize($fileInfo)
    {
        //获取表格的大小，限制上传表格的大小5M
        return $fileInfo['size'];
    }

    /**
     * 获取文件后缀
     * @param $fileInfo
     * @return bool|string
     */
    protected static function getExt($fileInfo)
    {
        $ext = substr($fileInfo['name'], strrpos($fileInfo['name'], '.')+1);
        return $ext;
    }


    /**
     * 生成随机文件名
     * @return string
     */
    protected static function randFileName()
    {
        $fileName = date('YmdHis').rand(100000,999999);
        return $fileName;
    }


    // 获取文件在服务器的临时位置
    protected static function getTmpName($fileInfo)
    {
        // 判断文件md5是否在数据库里
        // md5_file($fileInfo['tmp_name']);
        // 如果在数据库直接返回数据库记录
        // 不在数据库就存储
        return $fileInfo['tmp_name'];

    }


    /**
     * 获取文件名
     * @param $fileInfo 上传的文件信息
     * @param string $isOriginal
     * @return string
     */
    protected static function getFileName($fileInfo, $isOriginal = '')
    {
        $originalName = $fileInfo['name'];
        // 上传的文件名是否使用原来的文件名字
        if($isOriginal){
            $fileName = $originalName;
        } else {
            $randName = self::randFileName();
            $ext = self::getExt($fileInfo);
            $fileName =  $randName . '.' . $ext;
        }
        return $fileName;
    }


    /**
     * 上传文件到阿里云
     * @param string $fileName  想要保存文件的名称
     * @param string $tmpPath   上传的文件在服务器的临时文件地址
     * @return array|null 返回阿里云的文件全路径/错误信息
     * @throws \OSS\Core\OssException
     */
    protected static function uploadToOss($fileName, $tmpPath)
    {
        $oss  = (new OssService())->ossUpload($fileName, $tmpPath);
        if(array_key_exists('error', $oss)){
            return $oss;
        } else {
            return $oss['info']['url'];
        }
    }


    /**
     * 上传文件到本地
     * @param string $fileName  文件要保存的名称
     * @param string $tmpPath   上传的文件信息的临时保存地址
     * @param string $saveFilePath 保存路径(默认是图片路径)
     * @param string $prefix 文件读取的前缀(默认是图片的前缀)
     * @return string 路径+文件名
     * @throws FileUploadException
     */
    protected static function uploadToLocal($fileName, $tmpPath, $saveFilePath = '', $prefix = '')
    {
        if(!$saveFilePath){
            $savePath = config('program.static_image');
        } else {
            $savePath = $saveFilePath;
        }
        if(!$prefix){
            $pre =  config('program.img_prefix');
        } else {
            $pre = $prefix;
        }
        if(!is_dir($savePath)){
            mkdir($savePath, 0777, true);
        }
        $url = $savePath.$fileName;
        $res = move_uploaded_file($tmpPath, $url);
        if($res){
            return $pre.$fileName;
        } else {
            throw new FileUploadException([
                'message'=> '上传文件失败',
                'errorCode'=> 60004
            ]);
        }
    }


    /**
     * 上传文件
     * @param $fileInfo 文件信息
     * @param string $isOriginal   是否使用原文件名(默认否)
     * @param int $OssOrLocal      1上传到本地/2上传到阿里云OSS
     * @param string $saveFilePath  保存在本地的地址
     * @param string $prefix        保存的文件的前缀
     * @return mixed                返回数组, url全名称, fileName文件名
     * @throws FileUploadException
     * @throws \OSS\Core\OssException
     */
    public static function upload($fileInfo, $isOriginal = '', $OssOrLocal = 1, $saveFilePath = '', $prefix = '')
    {
        $fileName = self::getFileName($fileInfo,$isOriginal);
        $tmpPath = self::getTmpName($fileInfo);
        if($OssOrLocal == 1){
            $f['url'] = self::uploadToLocal($fileName, $tmpPath, $saveFilePath, $prefix);
        } else if($OssOrLocal == 2){
            $f['url'] = self::uploadToOss($fileName, $tmpPath);
        } else {
            throw new FileUploadException([
                'message'=> '未选择上传地址',
                'errorCode' => 60003
            ]);
        }
        $f['fileName'] = $fileName;
        return $f;
    }
}