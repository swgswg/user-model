<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 15:59
 */

namespace app\api\service;

//require_once __DIR__ . '../../../extend/OSS/autoload.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Core\OssUtil;

class OssService
{
    private $accessKeyId;
    private $accessKeySecret;
    private $endpoint;
    private $bucket;

    public function __construct()
    {
        $this->accessKeyId     = config('oss.oss_access_id');
        $this->accessKeySecret = config('oss.oss_access_key');
        $this->endpoint        = config('oss.oss_endpoint');
        $this->bucket          = config('oss.oss_bucket');
    }

    /**
     * Get an OSSClient instance according to config.
     *
     * @return OssClient An OssClient instance
     */
    public function getOssClient()
    {
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, false);
        } catch (OssException $e) {
//            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
//            printf($e->getMessage() . "\n");
            return ['error'=> json_encode('creating OssClient instance: FAILED=='.$e->getMessage())];
        }
        return $ossClient;
    }


    /**
     * 上传文件到oss
     * @param $object   想要保存文件的名称
     * @param $filePath 文件路径
     * @throws OssException
     * @return array|null
     */
    public function ossUpload($object, $filePath)
    {

        try{
            $ossClient = $this->getOssClient();
            $res = $ossClient->uploadFile($this->getBucketName(), $object, $filePath);
            return $res;
        } catch(OssException $e) {
            return ['error'=>json_encode($e->getMessage())];
        }
    }

    public function getBucketName()
    {
        return $this->bucket;
    }

    /**
     * A tool function which creates a bucket and exists the process if there are exceptions
     */
    public function createBucket()
    {
        $ossClient = $this->getOssClient();
        if (array_key_exists('error',$ossClient)){
            return false;
        }
        $bucket = $this->getBucketName();
        $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ;
        try {
            $ossClient->createBucket($bucket, $acl);
        } catch (OssException $e) {

            $message = $e->getMessage();
            if (OssUtil::startsWith($message, 'http status: 403')) {
//                echo "Please Check your AccessKeyId and AccessKeySecret" . "\n";
                return ['error'=>'Please Check your AccessKeyId and AccessKeySecret'];
            } elseif (strpos($message, "BucketAlreadyExists") !== false) {
//                echo "Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint. " . "\n";
                return ['error'=> 'Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint.'];
            }
//            printf(__FUNCTION__ . ": FAILED\n");
//            printf($e->getMessage() . "\n");
            return ['error'=> json_encode($e->getMessage())];
        }
//        print(__FUNCTION__ . ": OK" . "\n");
        return true;
    }

    public function println($message)
    {
        if (!empty($message)) {
            echo strval($message) . "\n";
        }
    }
}