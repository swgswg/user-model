<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 22:42
 */

namespace app\api\service;

use app\api\validate\ImageValidate;

class ImageUpload extends File
{
    // 'name' => string '005695109356f96.jpg' (length=19)
    // 'type' => string 'image/jpeg' (length=10)
    // 'tmp_name' => string 'C:\Windows\php5AA9.tmp' (length=22)
    // 'error' => int 0
    // 'size' => int 31441

    /**
     * 单文件上传
     * @param $file  文件信息
     * @param $isOriginal 是否使用原文件名
     * @param $OssOrLocal 上传到OSS还是本地
     * @return mixed
     */
    public static function oneFile($file, $isOriginal, $OssOrLocal)
    {
        $f = self::imageUp($file, $isOriginal, $OssOrLocal);
        return $f;
    }


    /**
     * 多文件上传
     * @param $file   文件信息
     * @param $isOriginal 是否使用原文件名
     * @param $OssOrLocal 上传到OSS还是本地
     * @return array
     */
    public static function moreFile($file, $isOriginal, $OssOrLocal)
    {
        $f = [];
        foreach ($file as $k=>$v){
            $f[] = self::imageUp($v, $isOriginal, $OssOrLocal);
        }
        return $f;
    }


    private static function checkImg($fileInfo)
    {
        $info['size']   = self::getSize($fileInfo);
        $info['ext']    = self::getExt($fileInfo);
        $info['tmp_name'] = $fileInfo['tmp_name'];
        (new ImageValidate())->checkParam($info);
    }


    // 文件上传
    private static function imageUp($file, $isOriginal = 0, $OssOrLocal = 1)
    {
        $fileInfo = $file->getInfo();
        self::checkImg($fileInfo);

        $saveFilePath = config('program.static_image');
        $prefix = config('program.img_prefix');
        $f = self::upload($fileInfo, $isOriginal, $OssOrLocal, $saveFilePath, $prefix);
        return $f;
    }

}