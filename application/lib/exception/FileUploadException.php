<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 20:03
 */

namespace app\lib\exception;


class FileUploadException extends BaseException
{
    public $message = '上传文件失败';
    public $errorCode = 60000;
}